/**
 * Featured Brands Horizontal Scroll and Drag Interactions
 */
document.addEventListener('DOMContentLoaded', function () {
    const scrollContainers = document.querySelectorAll('.brands-scroll-container');

    scrollContainers.forEach(container => {
        const targetId = container.id;
        const prevBtn = document.querySelector(`.brand-nav-btn[data-target="${targetId}"][data-dir="prev"]`);
        const nextBtn = document.querySelector(`.brand-nav-btn[data-target="${targetId}"][data-dir="next"]`);

        function updateButtons() {
            if (!prevBtn || !nextBtn) return;
            const maxScroll = Math.max(0, container.scrollWidth - container.clientWidth);
            if (maxScroll <= 5) {
                prevBtn.disabled = true;
                nextBtn.disabled = true;
                prevBtn.classList.add('btn-inactive');
                nextBtn.classList.add('btn-inactive');
            } else {
                prevBtn.classList.remove('btn-inactive');
                nextBtn.classList.remove('btn-inactive');
                prevBtn.disabled = container.scrollLeft <= 5;
                nextBtn.disabled = container.scrollLeft >= maxScroll - 5;
            }
        }

        if (prevBtn) {
            prevBtn.addEventListener('click', () => {
                const item = container.querySelector('.brand-scroll-item');
                const cardWidth = item ? item.getBoundingClientRect().width + 24 : 260;
                container.scrollBy({ left: -(cardWidth * 2), behavior: 'smooth' });
            });
        }

        if (nextBtn) {
            nextBtn.addEventListener('click', () => {
                const item = container.querySelector('.brand-scroll-item');
                const cardWidth = item ? item.getBoundingClientRect().width + 24 : 260;
                container.scrollBy({ left: (cardWidth * 2), behavior: 'smooth' });
            });
        }

        container.addEventListener('scroll', updateButtons, { passive: true });
        window.addEventListener('resize', updateButtons);
        // Initial button check
        updateButtons();
        // Check after images load as scrollWidth might update
        window.addEventListener('load', updateButtons);

        // Mouse Drag to Scroll
        let isDown = false;
        let startX = 0;
        let scrollStart = 0;
        let moved = false;

        container.addEventListener('mousedown', (e) => {
            isDown = true;
            moved = false;
            startX = e.pageX - container.offsetLeft;
            scrollStart = container.scrollLeft;
            container.style.scrollBehavior = 'auto'; // Disable smooth during drag for responsive tracking
        });

        const stopDrag = () => {
            if (!isDown) return;
            isDown = false;
            container.style.scrollBehavior = 'smooth';
            setTimeout(() => { moved = false; }, 50);
        };

        container.addEventListener('mouseleave', stopDrag);
        container.addEventListener('mouseup', stopDrag);

        container.addEventListener('mousemove', (e) => {
            if (!isDown) return;
            const x = e.pageX - container.offsetLeft;
            const walk = (x - startX);
            if (Math.abs(walk) > 4) {
                moved = true;
                e.preventDefault();
                container.scrollLeft = scrollStart - walk;
            }
        });

        // Prevent accidental link click when dragging
        container.querySelectorAll('a').forEach(link => {
            link.addEventListener('click', (e) => {
                if (moved) {
                    e.preventDefault();
                    e.stopImmediatePropagation();
                }
            });
        });
    });
});
