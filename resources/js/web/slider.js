document.addEventListener('alpine:init', () => {
    Alpine.data('categoryBubbleSlider', (total = 0) => ({
        total,
        visibleCount: 5,
        startIndex: 0,
        intervalId: null,

        init() {
            if (this.total <= this.visibleCount) {
                return;
            }

            this.intervalId = setInterval(() => {
                this.startIndex = (this.startIndex + 1) % this.total;
            }, 500);
        },

        isVisible(index) {
            if (this.total <= this.visibleCount) {
                return true;
            }

            const end = this.startIndex + this.visibleCount;

            if (end <= this.total) {
                return index >= this.startIndex && index < end;
            }

            return index >= this.startIndex || index < (end - this.total);
        },

        destroy() {
            if (this.intervalId) {
                clearInterval(this.intervalId);
                this.intervalId = null;
            }
        },
    }));

    Alpine.data('productCarousel', () => ({
        currentPage: 0,
        pageCount: 1,
        pages: [],
        autoPlayInterval: null,
        
        init() {
            this.$nextTick(() => {
                this.updatePagination();
                window.addEventListener('resize', () => {
                    this.updatePagination();
                    this.handleAutoPlay();
                });
                
                // Update pagination after images load
                const carousel = this.$refs.carousel;
                const images = carousel.querySelectorAll('img');
                images.forEach(img => {
                    img.addEventListener('load', () => this.updatePagination());
                });

                this.handleAutoPlay();
            });
        },

        isDesktop() {
            return window.innerWidth >= 1024;
        },

        handleAutoPlay() {
            if (this.autoPlayInterval) {
                clearInterval(this.autoPlayInterval);
                this.autoPlayInterval = null;
            }

            if (!this.isDesktop()) return;

            this.autoPlayInterval = setInterval(() => {
                if (this.pageCount <= 1) return;
                const nextPage = (this.currentPage + 1) % this.pageCount;
                this.goToPage(nextPage);
            }, 1500);
        },

        destroy() {
            if (this.autoPlayInterval) {
                clearInterval(this.autoPlayInterval);
                this.autoPlayInterval = null;
            }
        },
        
        scroll(direction) {
            const carousel = this.$refs.carousel;
            const card = carousel.querySelector('.product-card');
            
            if (card) {
                // Get the actual card width including gap
                const cardWidth = card.offsetWidth;
                const gap = parseInt(window.getComputedStyle(carousel).gap) || 24;
                const scrollAmount = cardWidth + gap;
                
                carousel.scrollBy({
                    left: direction * scrollAmount,
                    behavior: 'smooth'
                });
                
                // Update pagination after scroll completes
                setTimeout(() => this.updatePagination(), 300);
            }
        },

        goToPage(pageIndex) {
            const carousel = this.$refs.carousel;
            if (!carousel) return;

            const clampedPage = Math.max(0, Math.min(pageIndex, this.pageCount - 1));
            const pageWidth = carousel.clientWidth;

            carousel.scrollTo({
                left: clampedPage * pageWidth,
                behavior: 'smooth',
            });

            this.currentPage = clampedPage;
        },

          isRTL() {
        return document.documentElement.dir === 'rtl' || 
               document.body.dir === 'rtl' ||
               getComputedStyle(document.documentElement).direction === 'rtl';
    },

        updatePagination() {
            const carousel = this.$refs.carousel;
            if (!carousel) return;
            
            const maxScroll = Math.max(0, carousel.scrollWidth - carousel.clientWidth);

            if (maxScroll <= 0) {
                this.pageCount = 1;
                this.currentPage = 0;
                this.pages = [0];
                return;
            }

            const pageWidth = carousel.clientWidth || 1;
            this.pageCount = Math.ceil(maxScroll / pageWidth) + 1;
            this.pages = Array.from({ length: this.pageCount }, (_, i) => i);
            this.currentPage = Math.max(0, Math.min(this.pageCount - 1, Math.round(carousel.scrollLeft / pageWidth)));
        }
    }));
});