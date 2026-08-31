
document.addEventListener('alpine:init' , () => {
    
    Alpine.data('productCard' , (type , product_id, inStock = true, maxStock = 99) => {
          return {
                adding: false,
                showSuccess: false,
                errorMessage: null,
                quantity: 1,
                inStock: inStock === true || inStock === 'true',
                maxStock: (() => {
                    const parsedStock = parseInt(maxStock, 10);
                    return Number.isNaN(parsedStock) ? 99 : parsedStock;
                })(),
                
                showError(message) {
                    this.errorMessage = message;
                    setTimeout(() => {
                        this.errorMessage = null;
                    }, 3000);
                },

                incrementQuantity() {
                    if (this.quantity < this.maxStock) {
                        this.quantity = Number(this.quantity) + 1;
                    }
                },

                decrementQuantity() {
                    if (this.quantity > 1) {
                        this.quantity = Number(this.quantity) - 1;
                    }
                },

                async addToCart() {
                    if (this.adding) return;
                    if (!this.inStock) {
                        this.showError('This item is out of stock.');
                        return;
                    }
                    
                    this.adding = true;
                    
                    try {
                        // Make AJAX request to add to cart
                        const response = await fetch('/cart/add', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                            },
                            body: JSON.stringify({
                                type : type,
                                product_id: product_id,
                                quantity: this.quantity
                            })
                        });

                        const data = await response.json();

                        if (data.success) {
                            // Update cart count
                            const newCount = data.data.cart_count;
                            
                            this.updateCartCount(newCount);
                            
                            // Show success message
                            this.showSuccess = true;
                            setTimeout(() => {
                                this.showSuccess = false;
                            }, 2000);
                        } else {
                            this.showError(data.message || 'Failed to add item to cart');
                        }
                    } catch (error) {
                        console.error('Error adding to cart:', error);
                        this.showError('An error occurred. Please try again.');
                    } finally {
                        this.adding = false;
                    }
                },
                
                async buyNow() {
                    if (this.adding) return;
                    if (!this.inStock) {
                        this.showError('This item is out of stock.');
                        return;
                    }
                    
                    this.adding = true;
                    
                    try {
                        const response = await fetch('/cart/add', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                            },
                            body: JSON.stringify({
                                type: type,
                                product_id: product_id,
                                quantity: this.quantity
                            })
                        });

                        const data = await response.json();

                        if (data.success) {
                            this.updateCartCount(data.data.cart_count);
                            window.location.href = '/checkout';
                        } else {
                            this.showError(data.message || 'Failed to add item to cart');
                        }
                    } catch (error) {
                        console.error('Error:', error);
                        this.showError('An error occurred. Please try again.');
                    } finally {
                        this.adding = false;
                    }
                },

                updateCartCount(count) {
                    // Dispatch event to update nav
                    window.dispatchEvent(new CustomEvent('cart-updated', {
                        detail: { count: parseInt(count) }
                    }));
                }
            }
    })
})