document.addEventListener('alpine:init', () => {
    Alpine.data('checkoutForm', (total = 0, userData = {}, hasDeliveryOption = false, couponsEnabled = false) => ({
        form: {
            name: userData.name || '',
            email: userData.email || '',
            phone: userData.phone || '',
            address: userData.address || '',
            city_id: userData.city_id || '',
            insta_account: userData.insta_account || '',
            delivery_option: '',
            payment_method: 'cash_on_delivery',
            payment_proof: null,
            notes: '',
        },
        coupon: {
            code: '',
            loading: false,
            applied: false,
            discountPercentage: 0,
            message: '',
            error: '',
        },
        errors: {
            name: '',
            email: '',
            phone: '',
            address: '',
            city_id: '',
            delivery_option: '',
            payment_method: '',
            payment_proof: '',
            insta_account: '',
        },
        total,
        deliveryPrice: 0,
        selectedCityHasDiscussion: false,
        showDeliveryOptions: false,
        hasDeliveryOption,
        couponsEnabled,
        validation: userData.validation || {},
        loading: false,
        success: false,
        serverError: '',
        isFirstOrder: Boolean(userData.isFirstOrder),
        orderId: '',

        init() {
            this.checkCityDeliveryOptions();
        },

        message(key) {
            return this.validation[key] || key;
        },

        checkCityDeliveryOptions() {
            this.$refs.citySelect.value = this.form.city_id || '';
            const selectedOption = this.$refs.citySelect.selectedOptions[0];
            if (!selectedOption || !selectedOption.value) {
                this.showDeliveryOptions = false;
                this.selectedCityHasDiscussion = false;
                this.deliveryPrice = 0;
                this.form.delivery_option = '';
                return;
            }

            this.selectedCityHasDiscussion = selectedOption.dataset.hasDiscussion === 'true';
            this.deliveryPrice = parseFloat(selectedOption.dataset.deliveryPrice) || 0;
            this.showDeliveryOptions = true;

            if (this.selectedCityHasDiscussion) {
                this.form.delivery_option = '';
                this.errors.delivery_option = '';
            } else {
                this.form.delivery_option = 'proceed';
                this.errors.delivery_option = '';
            }
        },

        onCityChange() {
            this.validateField('city_id');
            this.checkCityDeliveryOptions();
        },

        setPaymentProof(event) {
            this.form.payment_proof = event.target.files?.[0] || null;
            this.validateField('payment_proof');
        },

        shouldShowDeliveryFee() {
            return this.deliveryPrice > 0
                && (this.form.delivery_option !== 'discuss'
                    || (this.showDeliveryOptions && !this.selectedCityHasDiscussion));
        },

        calculateTotal() {
            let finalTotal = this.total;

            if (this.shouldShowDeliveryFee()) {
                finalTotal += this.deliveryPrice;
            }
            if (finalTotal > this.total && this.isFirstOrder) {
                finalTotal -= this.deliveryPrice;
            }

            finalTotal -= this.couponDiscount();

            return Math.max(0, finalTotal);
        },

        couponDiscount() {
            if (!this.coupon.applied || !this.coupon.discountPercentage) return 0;
            return Math.round((this.total * (this.coupon.discountPercentage / 100)) * 100) / 100;
        },

        async applyCoupon() {
            if (!this.couponsEnabled) return;

            const code = this.coupon.code.trim();
            if (!code) {
                this.coupon.error = this.message('coupon_required');
                return;
            }

            this.coupon.loading = true;
            this.coupon.error = '';

            try {
                const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content;
                const response = await fetch('/coupon/validate', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        Accept: 'application/json',
                        'X-CSRF-TOKEN': csrfToken,
                    },
                    body: JSON.stringify({ code }),
                });
                const data = await response.json();

                if (response.ok && data.valid) {
                    this.coupon.applied = true;
                    this.coupon.discountPercentage = data.discount_percentage;
                    this.coupon.message = data.message;
                    this.coupon.error = '';
                } else {
                    this.coupon.applied = false;
                    this.coupon.discountPercentage = 0;
                    this.coupon.error = data.message || this.message('coupon_invalid');
                }
            } catch (error) {
                this.coupon.error = this.message('network_error');
            } finally {
                this.coupon.loading = false;
            }
        },

        removeCoupon() {
            if (!this.couponsEnabled) return;
            this.coupon.applied = false;
            this.coupon.code = '';
            this.coupon.discountPercentage = 0;
            this.coupon.message = '';
            this.coupon.error = '';
        },

        validateField(field) {
            this.errors[field] = '';

            switch (field) {
                case 'name':
                    if (!this.form.name.trim()) {
                        this.errors.name = this.message('name_required');
                    } else if (this.form.name.trim().length < 3) {
                        this.errors.name = this.message('name_min');
                    }
                    break;
                case 'email':
                    if (!this.form.email.trim()) {
                        this.errors.email = this.message('email_required');
                    } else if (!this.isValidEmail(this.form.email)) {
                        this.errors.email = this.message('email_invalid');
                    }
                    break;
                case 'phone':
                    if (!this.form.phone.trim()) {
                        this.errors.phone = this.message('phone_required');
                    }
                    break;
                case 'address':
                    if (!this.form.address.trim()) {
                        this.errors.address = this.message('address_required');
                    } else if (this.form.address.trim().length < 10) {
                        this.errors.address = this.message('address_min');
                    }
                    break;
                case 'city_id':
                    if (!this.form.city_id) {
                        this.errors.city_id = this.message('city_required');
                    }
                    break;
                case 'delivery_option':
                    if (this.showDeliveryOptions && this.selectedCityHasDiscussion && !this.form.delivery_option) {
                        this.errors.delivery_option = this.message('delivery_option_required');
                    }
                    break;
                case 'payment_method':
                    if (!this.form.payment_method) {
                        this.errors.payment_method = this.message('payment_method_required');
                    }
                    break;
                case 'payment_proof':
                    this.errors.payment_proof = this.form.payment_method === 'instapay' && !this.form.payment_proof
                        ? this.message('payment_proof_required')
                        : '';
                    break;
            }
        },

        validateAll() {
            ['name', 'email', 'phone', 'address', 'city_id', 'payment_method'].forEach((field) => {
                this.validateField(field);
            });

            if (this.showDeliveryOptions && this.selectedCityHasDiscussion) {
                this.validateField('delivery_option');
            }
            this.validateField('payment_proof');

            return !Object.values(this.errors).some((error) => error !== '');
        },

        isValidEmail(email) {
            return /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email);
        },

        async submitOrder() {
            if (this.loading || this.success) return;
            this.serverError = '';

            if (!this.validateAll()) {
                await this.$nextTick();
                document.querySelector('[aria-invalid="true"]')?.scrollIntoView({
                    behavior: 'smooth',
                    block: 'center',
                });
                return;
            }

            this.loading = true;

            try {
                const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content;
                const formData = new FormData();
                formData.append('name', this.form.name);
                formData.append('email', this.form.email);
                formData.append('phone', this.form.phone);
                formData.append('address', this.form.address);
                formData.append('city_id', this.form.city_id);
                formData.append('delivery_option', this.form.delivery_option || 'proceed');
                formData.append('payment_method', this.form.payment_method);
                formData.append('notes', this.form.notes);

                if (this.couponsEnabled && this.coupon.applied) {
                    formData.append('coupon_code', this.coupon.code.trim().toUpperCase());
                }
                if (this.form.payment_method === 'instapay') {
                    formData.append('insta_account', this.form.insta_account);
                    if (this.form.payment_proof) {
                        formData.append('payment_proof', this.form.payment_proof);
                    }
                }

                const response = await fetch('/order', {
                    method: 'POST',
                    headers: {
                        Accept: 'application/json',
                        'X-CSRF-TOKEN': csrfToken,
                    },
                    body: formData,
                });
                const data = await response.json();

                if (!response.ok) {
                    if (data.errors) {
                        Object.entries(data.errors).forEach(([key, messages]) => {
                            if (Object.prototype.hasOwnProperty.call(this.errors, key)) {
                                this.errors[key] = Array.isArray(messages) ? messages[0] : messages;
                            }
                        });
                    } else {
                        this.serverError = data.message || this.message('order_error');
                    }
                    await this.$nextTick();
                    document.querySelector('[aria-invalid="true"]')?.scrollIntoView({
                        behavior: 'smooth',
                        block: 'center',
                    });
                    return;
                }

                this.success = true;
                this.orderId = data.order_id || data.id || '';
                window.scrollTo({ top: 0, behavior: 'smooth' });

                window.setTimeout(() => {
                    if (data.payment_method === 'instapay' && userData.instapay) {
                        window.open(userData.instapay, '_blank', 'noopener');
                    }
                    window.location.href = data.redirect_url || window.location.href;
                }, 1500);
            } catch (error) {
                this.serverError = this.message('network_error');
            } finally {
                if (!this.success) this.loading = false;
            }
        },
    }));
});
