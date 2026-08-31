<div id="checkoutModal" x-data="checkoutForm({{ $total }})" class="hidden fixed inset-0 backdrop-blur-lg bg-opacity-30 z-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-lg shadow-xl max-w-md w-full max-h-[90vh] overflow-y-auto">
        <div class="p-6">
            <div class="flex justify-between items-center mb-6">
                <h2 class="text-2xl font-bold text-gray-800">Checkout</h2>
                <button @click="closeModal()" class="text-gray-500 hover:text-gray-700">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>

            <!-- Success Message -->
            <div x-show="success" x-transition class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-lg">
                <div class="flex items-center">
                    <svg class="w-5 h-5 me-2" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                    </svg>
                    <span>Order placed successfully check the email inbox ..!</span>
                </div>
            </div>

            <form @submit.prevent="submitOrder">
                
                <!-- Name -->
                <div class="mb-4">
                    <label for="name" class="block text-gray-700 font-medium mb-2">Full Name *</label>
                    <input 
                        type="text" 
                        id="name" 
                        x-model="form.name"
                        :class="{ 'border-red-500': errors.name }"
                        @blur="validateField('name')"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                        placeholder="John Doe"
                    >
                    <p x-show="errors.name" x-text="errors.name" class="text-red-500 text-sm mt-1"></p>
                </div>

                <!-- Email -->
                <div class="mb-4">
                    <label for="email" class="block text-gray-700 font-medium mb-2">Email Address *</label>
                    <input 
                        type="email" 
                        id="email" 
                        x-model="form.email"
                        :class="{ 'border-red-500': errors.email }"
                        @blur="validateField('email')"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                        placeholder="john@example.com"
                    >
                    <p x-show="errors.email" x-text="errors.email" class="text-red-500 text-sm mt-1"></p>
                </div>

                <!-- Phone -->
                <div class="mb-4">
                    <label for="checkout_phone" class="block text-gray-700 font-medium mb-2">Phone Number *</label>
                    <input 
                        type="tel" 
                        id="checkout_phone" 
                        x-model="form.phone"
                        :class="{ 'border-red-500': errors.phone }"
                        @blur="validateField('phone')"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                        placeholder="+201148992811"
                    >
                    <p x-show="errors.phone" x-text="errors.phone" class="text-red-500 text-sm mt-1"></p>
                </div>

                <!-- Address -->
                <div class="mb-4">
                    <label for="address" class="block text-gray-700 font-medium mb-2">Address *</label>
                    <input 
                        type="text" 
                        id="address" 
                        x-model="form.address"
                        :class="{ 'border-red-500': errors.address }"
                        @blur="validateField('address')"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                        placeholder="مثال : شارع النصر , مدينة نصر - القاهرة"
                    >
                    <p x-show="errors.address" x-text="errors.address" class="text-red-500 text-sm mt-1"></p>
                </div>

                <!-- Payment Method -->
                <div class="mb-6">
                    <label class="block text-gray-700 font-medium mb-3">Payment Method *</label>
                    
                    <div class="space-y-3">
                        <label 
                            class="flex items-start p-3 border rounded-lg cursor-pointer hover:bg-gray-50 transition"
                            :class="form.payment_method === 'cash_on_delivery' ? 'border-blue-500 bg-blue-50' : 'border-gray-300'"
                            @click="form.payment_method = 'cash_on_delivery'; validateField('payment_method')"
                        >
                            <input 
                                type="radio" 
                                name="payment_method" 
                                value="cash_on_delivery"
                                x-model="form.payment_method"
                                class="mt-1 me-3"
                            >
                            <div>
                                <div class="font-medium text-gray-800">Cash on Delivery</div>
                                <div class="text-sm text-gray-500">Pay when you receive your order</div>
                            </div>
                        </label>

                        <label 
                            class="flex items-start p-3 border rounded-lg cursor-pointer hover:bg-gray-50 transition"
                            :class="form.payment_method === 'instapay' ? 'border-blue-500 bg-blue-50' : 'border-gray-300'"
                            @click="form.payment_method = 'instapay'; validateField('payment_method')"
                        >
                            <input 
                                type="radio" 
                                name="payment_method" 
                                value="instapay"
                                x-model="form.payment_method"
                                class="mt-1 me-3"
                            >
                            <div>
                                <div class="font-medium text-gray-800">InstaPay</div>
                                <div class="text-sm text-gray-500">Pay instantly via InstaPay</div>
                            </div>
                        </label>
                    </div>
                    <p x-show="errors.payment_method" x-text="errors.payment_method" class="text-red-500 text-sm mt-1"></p>
                </div>

                <!-- Order Total -->
                <div class="bg-gray-50 p-4 rounded-lg mb-6">
                    <div class="flex justify-between text-lg font-bold">
                        <span>Order Total:</span>
                        <span class="text-green-600">EGP<span x-text="total.toFixed(2)"></span></span>
                    </div>
                </div>

                <!-- Submit Button -->
                <button 
                    type="submit" 
                    :disabled="loading"
                    :class="loading ? 'bg-gray-400 cursor-not-allowed' : 'bg-blue-600 hover:bg-blue-700'"
                    class="w-full text-white py-3 rounded-lg transition font-semibold flex items-center justify-center"
                >
                    <span x-show="!loading">Place Order</span>
                    <span x-show="loading" class="flex items-center">
                        <svg class="animate-spin h-5 w-5 me-2" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        Processing...
                    </span>
                </button>
            </form>
        </div>
    </div>
</div>