var MenuItems = document.getElementById("MenuItems");

MenuItems.style.maxHeight = "0px";

function menuToggle() {
    if(MenuItems.style.maxHeight = "0px") {
        MenuItems.style.maxHeight = "200px";
    }
    else {
        MenuItems.style.maxHeight = "0px";
    }   
}

new Swiper('.card-wrapper', {
    loop: true,
    spaceBetween: 30,
  
    // Pagination Bullets
    pagination: {
      el: '.swiper-pagination',
      clickable: true,
      dynamicBullets: true,
    },
  
    // Navigation arrows
    navigation: {
      nextEl: '.swiper-button-next',
      prevEl: '.swiper-button-prev',
    },

    breakpoints: {
        0: {
            slidesPerView: 1
        },
        768: {
            slidesPerView: 2
        },
        1024: {
            slidesPerView: 3
        },
        1280: {
            slidesPerView: 4
        },
        1536: {
            slidesPerView: 5
        }, 
        1800: {
            slidesPerView: 5
        }, 
    }
  });



  new Swiper('.colWrapper', {
    loop: true,
    spaceBetween: 10,
  
    // Pagination Bullets
    pagination: {
      el: '.swiper-pagination',
      clickable: true,
      dynamicBullets: true,
    },
  
    // Navigation arrows
    navigation: {
      nextEl: '.swiper-button-next',
      prevEl: '.swiper-button-prev',
    },

    breakpoints: {
        0: {
            slidesPerView: 1
        },
        768: {
            slidesPerView: 2
        },
        1024: {
            slidesPerView: 3
        },
    }
  });


   document.addEventListener('DOMContentLoaded', () => {
            const tabs = document.querySelectorAll('.filter-tab');
            const products = document.querySelectorAll('.product-card');
        
            tabs.forEach(tab => {
                tab.addEventListener('click', (e) => {
                    e.preventDefault();
                    const filter = tab.dataset.filter;
        
                    // Remove active class from all tabs
                    tabs.forEach(t => t.classList.remove('active-tab'));
                    // Add active class to clicked tab
                    tab.classList.add('active-tab');
        
                    // Show/hide products based on filter
                    products.forEach(product => {
                        const category = product.dataset.category;
                        if (filter === 'all' || category === filter) {
                            product.style.display = 'block';
                        } else {
                            product.style.display = 'none';
                        }
                    });
                });
            });
        
            // Auto-select "All" on load
            const defaultTab = document.querySelector('.filter-tab[data-filter="all"]');
            if (defaultTab) defaultTab.click();
        });


  

