// Checkout form Alpine.js component
document.addEventListener("alpine:init", () => {
    Alpine.data('checkoutForm', (total = 0, userData = {}, hasDeliveryOption = false) => ({
        form: {
            name: userData.name || '',
            email: userData.email || '',
            phone: userData.phone || '',
            address: userData.address || '',
            city_id: userData.city_id || '',
            insta_account: userData.insta_account || '',
            delivery_option: '',
            payment_method: '',
            payment_proof: null,
            proofPreviewUrl: '',
            dragOver: false,
            notes: ''
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
        total: total,
        deliveryPrice: 0,
        selectedCityHasDiscussion: false,
        showDeliveryOptions: false,
        hasDeliveryOption: hasDeliveryOption,
        loading: false,
        success: false,
        isFirstOrder: userData.isFirstOrder,
        orderId: '',
        
        init() {
            this.checkCityDeliveryOptions();
        },

        setupPhoneInput() {
            return;
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

    const hasDiscussion = selectedOption.dataset.hasDiscussion === 'true';
    const deliveryPrice = parseFloat(selectedOption.dataset.deliveryPrice) || 0;

    this.selectedCityHasDiscussion = hasDiscussion;
    this.deliveryPrice = deliveryPrice;
    this.showDeliveryOptions = true;

    if (hasDiscussion) {
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
            const file = event.target.files?.[0] || null;
            this.form.payment_proof = file;
            this.updateProofPreview(file);
            this.dragOver = false;
        },

        handleDrop(event) {
            this.dragOver = false;
            const file = event.dataTransfer?.files?.[0] || null;
            if (file) {
                this.form.payment_proof = file;
                this.updateProofPreview(file);
                this.validateField('payment_proof');
            }
        },

        updateProofPreview(file) {
            if (!file || !file.type?.startsWith('image/')) {
                this.proofPreviewUrl = '';
                return;
            }

            const reader = new FileReader();
            reader.onload = (event) => {
                this.proofPreviewUrl = event.target?.result || '';
            };
            reader.readAsDataURL(file);
        },

        shouldShowDeliveryFee() {
            // Show delivery fee breakdown if:
            // 1. User selected "proceed" option (for cities with discussion)
            // 2. City doesn't have discussion (auto-added delivery)
            return this.deliveryPrice > 0 && 
                   (this.form.delivery_option !== 'discuss' || 
                   (this.showDeliveryOptions && !this.selectedCityHasDiscussion));
        },

        calculateTotal() {
            let finalTotal = this.total;
            
            // Add delivery price if:
            // 1. User selected "proceed" option
            // 2. OR city doesn't have discussion (auto-added)
            if (this.shouldShowDeliveryFee()) {
                finalTotal += this.deliveryPrice;
            }
            if(finalTotal > this.total && this.isFirstOrder){
                finalTotal -= this.deliveryPrice
            }

            // Subtract coupon discount (applied on the items subtotal)
            finalTotal -= this.couponDiscount();

            return Math.max(0, finalTotal);
        },

        couponDiscount() {
            if (!this.coupon.applied || !this.coupon.discountPercentage) return 0;
            return Math.round((this.total * (this.coupon.discountPercentage / 100)) * 100) / 100;
        },

        async applyCoupon() {
            const code = this.coupon.code.trim();
            if (!code) {
                this.coupon.error = 'Please enter a coupon code.';
                return;
            }

            this.coupon.loading = true;
            this.coupon.error = '';

            try {
                const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content ||
                                  document.querySelector('input[name="_token"]')?.value;

                const response = await fetch('/coupon/validate', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
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
                    this.coupon.error = data.message || data.errors?.code?.[0] || 'Invalid coupon code.';
                }
            } catch (e) {
                this.coupon.error = 'Network error. Please try again.';
            } finally {
                this.coupon.loading = false;
            }
        },

        removeCoupon() {
            this.coupon.applied = false;
            this.coupon.code = '';
            this.coupon.discountPercentage = 0;
            this.coupon.message = '';
            this.coupon.error = '';
        },
        
        validateField(field) {
            this.errors[field] = '';
            
            switch(field) {
                case 'name':
                    if (!this.form.name.trim()) {
                        this.errors.name = 'Full name is required';
                    } else if (this.form.name.trim().length < 3) {
                        this.errors.name = 'Full name must be at least 3 characters';
                    }
                    break;
                    
                case 'email':
                    if (!this.form.email.trim()) {
                        this.errors.email = 'Email address is required';
                    } else if (!this.isValidEmail(this.form.email)) {
                        this.errors.email = 'Please enter a valid email address';
                    }
                    break;
                    
                case 'phone':
                     
                    if (!this.form.phone.trim()) {
                        this.errors.phone = 'Phone number is required';
                    } else if (!this.isValidPhone(this.form.phone)) {
                        this.errors.phone = 'Please enter a valid phone number (begin with +20)';
                    }
                    break;
                    
                case 'address':
                    if (!this.form.address.trim()) {
                        this.errors.address = 'Address is required';
                    } else if (this.form.address.trim().length < 10) {
                        this.errors.address = 'Please provide a detailed address';
                    }
                    break;
                    
                case 'city_id':
                    if (!this.form.city_id) {
                        this.errors.city_id = 'Please select a city';
                    }
                    break;
                    
                case 'payment_method':
                    if (!this.form.payment_method) {
                        this.errors.payment_method = 'Please select a payment method';
                    }
                    break;

                case 'payment_proof':
                    if (this.form.payment_method === 'instapay' && !this.form.payment_proof) {
                        this.errors.payment_proof = 'Please upload the transfer proof for the 20% guarantee payment.';
                    } else {
                        this.errors.payment_proof = '';
                    }
                    break;
               
            }
        },
        
        validateAll() {
            this.validateField('name');
            this.validateField('email');
            this.validateField('phone');
            this.validateField('address');
            this.validateField('city_id');
            
            // Only validate delivery option if city has discussion option
            if (this.showDeliveryOptions && this.selectedCityHasDiscussion) {
                this.validateField('delivery_option');
            }
            
            this.validateField('payment_method');
            this.validateField('payment_proof');
            
            return !Object.values(this.errors).some(error => error !== '');
        },
        
        isValidEmail(email) {
            const regex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            return regex.test(email);
        },
        
        isValidPhone(phone) {
           return true;
        },
        
        async submitOrder() {
            // Validate all fields
            if (!this.validateAll()) {
                // Scroll to first error
                const firstError = document.querySelector('.text-red-500');
                if (firstError) {
                    firstError.scrollIntoView({ behavior: 'smooth', block: 'center' });
                }
                return;
            }
            
            this.loading = true;
            this.success = false;
            
            try {
                const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content || 
                                document.querySelector('input[name="_token"]')?.value;

                const formData = new FormData();
                formData.append('name', this.form.name);
                formData.append('email', this.form.email);
                formData.append('phone', this.form.phone);
                formData.append('address', this.form.address);
                formData.append('city_id', this.form.city_id);
                formData.append('delivery_option', this.form.delivery_option || 'proceed');
                formData.append('payment_method', this.form.payment_method);
                formData.append('insta_account', this.form.insta_account);
                formData.append('notes', this.form.notes);
                formData.append('coupon_code', this.coupon.applied ? this.coupon.code.trim().toUpperCase() : '');
                if (this.form.payment_proof) {
                    formData.append('payment_proof', this.form.payment_proof);
                }
                
                const response = await fetch('/order', {
                    method: 'POST',
                    headers: {
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': csrfToken
                    },
                    body: formData
                });
                
                const data = await response.json();
             
                if (response.ok) {
                    this.success = true;
                    this.orderId = data.order_id || data.id || 'N/A';
                    
                    // Scroll to success message
                    window.scrollTo({ top: 0, behavior: 'smooth' });
                    
                    // Handle redirect based on payment method
                    setTimeout(() => {
                        if(data.payment_method === 'instapay'){
                           window.open(userData.instapay, '_blank');
                        }
                        
                        // Redirect to order confirmation page or reload
                        if (data.redirect_url) {
                            window.location.href = data.redirect_url;
                        } else {
                            window.location.reload();
                        }
                         
                    }, 2000);
                    
                } else {
                    // Handle validation errors from server
                    if (data.errors) {
                        Object.keys(data.errors).forEach(key => {
                            if (this.errors.hasOwnProperty(key)) {
                                this.errors[key] = Array.isArray(data.errors[key]) 
                                    ? data.errors[key][0] 
                                    : data.errors[key];
                            }
                        });
                        
                        // Scroll to first error
                        setTimeout(() => {
                            const firstError = document.querySelector('.text-red-500');
                            if (firstError) {
                                firstError.scrollIntoView({ behavior: 'smooth', block: 'center' });
                            }
                        }, 100);
                    } else {
                        alert(data.message || 'An error occurred. Please try again.');
                    }
                }
                
            } catch (error) {
                console.error('Error submitting order:', error);
                alert('Network error. Please check your connection and try again.');
            } finally {
                this.loading = false;
            }
        }
    }));
});