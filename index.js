// Wait for DOM to be fully loaded
document.addEventListener('DOMContentLoaded', function() {
    // Animation for hero text on page load
    const heroText = document.querySelector('.hero-text');
    if (heroText) {
        // Initial state
        heroText.style.opacity = '0';
        heroText.style.transform = 'translateX(-100px)';
        
        // Animate to final state - SLOWER duration
        heroText.style.transition = 'opacity 2s ease, transform 2s ease';
        setTimeout(() => {
            heroText.style.opacity = '1';
            heroText.style.transform = 'translateX(0)';
        }, 100);
    }

    // Scroll-triggered animations - SLOWER durations
    const observerOptions = {
        threshold: 0.1,
        rootMargin: '0px 0px -50px 0px'
    };

    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                // Add animation class
                entry.target.classList.add('animate-in');
                
                // Specific animations for different elements - SLOWER
                if (entry.target.classList.contains('service-cards')) {
                    animateServiceCards(entry.target);
                } else if (entry.target.classList.contains('card')) {
                    animateCard(entry.target);
                } else if (entry.target.classList.contains('health-tip-card')) {
                    animateHealthTipCard(entry.target);
                } else if (entry.target.classList.contains('feedback-card')) {
                    animateFeedbackCard(entry.target);
                }
            }
        });
    }, observerOptions);

    // Observe elements for scroll animations
    const elementsToObserve = document.querySelectorAll('.service-cards, .card, .health-tip-card, .feedback-card, .innovation-text');
    elementsToObserve.forEach(el => observer.observe(el));

    // Animation functions - SLOWER durations
    function animateServiceCards(container) {
        const cards = container.querySelectorAll('.card');
        cards.forEach((card, index) => {
            setTimeout(() => {
                card.style.opacity = '0';
                card.style.transform = 'translateY(100px)';
                card.style.transition = 'opacity 2s ease, transform 2s ease';
                
                setTimeout(() => {
                    card.style.opacity = '1';
                    card.style.transform = 'translateY(0)';
                }, 100);
            }, index * 400); // Slower stagger
        });
    }

    function animateCard(card) {
        card.style.opacity = '0';
        card.style.transform = 'scale(0.9)';
        card.style.transition = 'opacity 2s ease, transform 2s ease';
        
        setTimeout(() => {
            card.style.opacity = '1';
            card.style.transform = 'scale(1)';
        }, 100);
    }

    function animateHealthTipCard(card) {
        card.style.opacity = '0';
        card.style.transform = 'translateY(100px)';
        card.style.transition = 'opacity 2s ease, transform 2s ease';
        
        setTimeout(() => {
            card.style.opacity = '1';
            card.style.transform = 'translateY(0)';
        }, 100);
    }

    function animateFeedbackCard(card) {
        card.style.opacity = '0';
        card.style.transform = 'translateX(-30px)';
        card.style.transition = 'opacity 1.4s ease, transform 1.4s ease';
        
        setTimeout(() => {
            card.style.opacity = '1';
            card.style.transform = 'translateX(0)';
        }, 100);
    }

    // Smooth scroll for navigation links
    const navLinks = document.querySelectorAll('a[href^="#"]');
    navLinks.forEach(link => {
        link.addEventListener('click', function(e) {
            e.preventDefault();
            const targetId = this.getAttribute('href');
            if (targetId && targetId !== '#') {
                const targetSection = document.querySelector(targetId);
                if (targetSection) {
                    targetSection.scrollIntoView({
                        behavior: 'smooth',
                        block: 'start'
                    });
                }
            }
        });
    });

    // Back to Top Button Functionality
    const backToTopBtn = document.getElementById('backToTop');
    if (backToTopBtn) {
        // Show/hide button based on scroll position
        window.addEventListener('scroll', () => {
            if (window.pageYOffset > 300) {
                backToTopBtn.style.opacity = '1';
                backToTopBtn.style.visibility = 'visible';
            } else {
                backToTopBtn.style.opacity = '0';
                backToTopBtn.style.visibility = 'hidden';
            }
        });

        // Scroll to top when button is clicked
        backToTopBtn.addEventListener('click', () => {
            window.scrollTo({
                top: 0,
                behavior: 'smooth'
            });
        });
    }

    // Parallax effect for hero section - SLOWER
    window.addEventListener('scroll', () => {
        const scrolled = window.pageYOffset;
        const heroImage = document.querySelector('.hero-image');
        if (heroImage) {
            heroImage.style.transition = 'transform 0.3s ease';
            heroImage.style.transform = `translateY(${scrolled * 0.1}px)`;
        }
    });

    // Alternative using anime.js if available - SLOWER durations
    if (typeof anime !== 'undefined') {
        // Scroll-triggered anime.js animations - SLOWER
        const scrollObserver = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    if (entry.target.classList.contains('hero-text')) {
                        anime({
                            targets: entry.target,
                            opacity: [0, 1],
                            translateX: [-100, 0],
                            duration: 2000, // SLOWER
                            easing: 'easeOutExpo'
                        });
                    } else if (entry.target.classList.contains('card')) {
                        anime({
                            targets: entry.target,
                            opacity: [0, 1],
                            translateY: [50, 0],
                            duration: 1500, // SLOWER
                            delay: anime.stagger(400), // SLOWER stagger
                            easing: 'easeOutQuad'
                        });
                    } else if (entry.target.classList.contains('health-tip-card')) {
                        anime({
                            targets: entry.target,
                            opacity: [0, 1],
                            translateY: [30, 0],
                            duration: 1800, // SLOWER
                            easing: 'easeOutQuad'
                        });
                    }
                }
            });
        }, { threshold: 0.2 });

        // Observe elements for anime.js animations
        document.querySelectorAll('.card, .health-tip-card, .feedback-card').forEach(el => {
            scrollObserver.observe(el);
        });
    }
});
