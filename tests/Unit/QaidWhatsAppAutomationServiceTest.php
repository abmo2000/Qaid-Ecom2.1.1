<?php

namespace Tests\Unit;

use App\Events\OrderCreated;
use App\Jobs\SendOrderToQaidAutomation;
use App\Listeners\OrderCreationListener;
use App\Models\Order;
use App\Models\User;
use App\Services\QaidWhatsAppAutomationService;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Queue;
use Tests\TestCase;

class QaidWhatsAppAutomationServiceTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        config([
            'services.qaid_automation.url' => 'https://automation.example.test/api/site/orders',
            'services.qaid_automation.secret' => 'test-secret',
            'mail.to.address' => 'admin@example.test',
        ]);
    }

    public function test_successful_submission_sends_expected_json_headers_and_event_id(): void
    {
        Http::fake([
            'https://automation.example.test/*' => Http::response(['accepted' => true], 202),
        ]);

        $order = $this->makeOrder();

        $this->assertTrue(app(QaidWhatsAppAutomationService::class)->sendOrder($order, 'ar'));

        Http::assertSent(function ($request) use ($order): bool {
            $payload = $request->data();

            return $payload === [
                'event' => 'order.created',
                'order_id' => 123,
                'order_number' => 'ORD-123',
                'customer_name' => 'Customer Name',
                'phone' => '201012345678',
                'total' => 1500.0,
                'currency' => 'EGP',
                'locale' => 'ar',
                'created_at' => $order->created_at->toISOString(),
            ]
                && $request->header('X-QAID-SECRET') === ['test-secret']
                && $request->header('X-QAID-EVENT-ID') === ['order-123']
                && $request->header('Accept') === ['application/json']
                && $request->header('Content-Type') === ['application/json'];
        });
    }

    public function test_external_failure_is_rethrown_for_queue_retry_without_affecting_listener_dispatch(): void
    {
        Http::fake([
            'https://automation.example.test/*' => Http::response([], 503),
        ]);

        $this->expectException(\RuntimeException::class);
        app(QaidWhatsAppAutomationService::class)->sendOrder($this->makeOrder(), 'en');
    }

    public function test_order_created_listener_queues_automation_instead_of_direct_whatsapp_sending(): void
    {
        Queue::fake();
        Mail::fake();

        $order = $this->makeOrder();
        (new OrderCreationListener())->handle(new OrderCreated(collect(), 1500, $order));

        Queue::assertPushed(SendOrderToQaidAutomation::class, function ($job) use ($order): bool {
            return $job->order->id === $order->id && $job->locale === 'en';
        });
    }

    private function makeOrder(): Order
    {
        $order = new Order([
            'id' => 123,
            'order_id' => 'ORD-123',
            'amount' => 1500,
        ]);
        $order->setRawAttributes([
            'id' => 123,
            'order_id' => 'ORD-123',
            'amount' => 1500,
            'created_at' => Carbon::parse('2026-09-16T10:00:00+00:00'),
        ]);
        $order->setRelation('customer', new User([
            'name' => 'Customer Name',
            'email' => 'customer@example.test',
            'phone' => '+201012345678',
        ]));

        return $order;
    }
}