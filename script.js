// Mobile Navigation Toggle
const mobileMenuToggle = document.querySelector('.mobile-menu-toggle');
const sideNav = document.querySelector('.side-nav');

if (mobileMenuToggle && sideNav) {
    mobileMenuToggle.addEventListener('click', () => {
        sideNav.classList.toggle('active');
        mobileMenuToggle.classList.toggle('active');
    });
}

// Close mobile menu when clicking on a link
document.querySelectorAll('.nav-links a').forEach(link => {
    link.addEventListener('click', () => {
        if (sideNav) sideNav.classList.remove('active');
        if (mobileMenuToggle) mobileMenuToggle.classList.remove('active');
    });
});

// Active navigation highlighting for side nav
function updateActiveNav() {
    const sections = document.querySelectorAll('.section');
    const navItems = document.querySelectorAll('.nav-links .nav-link');
    
    let current = '';
    sections.forEach(section => {
        const sectionTop = section.offsetTop;
        const sectionHeight = section.clientHeight;
        if (scrollY >= (sectionTop - 300)) {
            current = section.getAttribute('id');
        }
    });

    navItems.forEach(item => {
        item.classList.remove('active');
        if (item.getAttribute('href') === `#${current}`) {
            item.classList.add('active');
        }
    });
}

// Scroll event listener
window.addEventListener('scroll', updateActiveNav);

// Smooth scrolling for navigation links
document.querySelectorAll('a[href^="#"]').forEach(anchor => {
    anchor.addEventListener('click', function (e) {
        e.preventDefault();
        const target = document.querySelector(this.getAttribute('href'));
        if (target) {
            target.scrollIntoView({
                behavior: 'smooth',
                block: 'start'
            });
        }
    });
});

// Form submission handler
const contactForm = document.querySelector('#contact-form');
if (contactForm) {
    contactForm.addEventListener('submit', function(e) {
        e.preventDefault();
        
        // Get form data
        const formData = new FormData(this);
        const data = Object.fromEntries(formData);
        
        // Here you would typically send the data to your server
        console.log('Form submitted:', data);
        
        // Show success message (you can customize this)
        alert('Thank you for your message! I will get back to you soon.');
        
        // Reset form
        this.reset();
    });
}

// Scroll-triggered animations
function animateOnScroll() {
    const elements = document.querySelectorAll('.work-item, .skill-group, .timeline-item, .contact-method');
    
    elements.forEach(element => {
        const elementTop = element.getBoundingClientRect().top;
        const elementVisible = 150;
        
        if (elementTop < window.innerHeight - elementVisible) {
            element.classList.add('animate');
        }
    });
}

window.addEventListener('scroll', animateOnScroll);

// Initialize page
document.addEventListener('DOMContentLoaded', function() {
    // Add loaded class to body for fade-in animation
    document.body.classList.add('loaded');
    
    // Initialize active nav
    updateActiveNav();
    
    // Initialize scroll animations
    animateOnScroll();
    
    // Initialize floating animations
    initFloatingElements();
    
    // Initialize skill bar animations
    initSkillBars();
});

// Floating elements animation
function initFloatingElements() {
    const floatItems = document.querySelectorAll('.float-item');
    floatItems.forEach((item, index) => {
        item.style.animationDelay = `${index * 2}s`;
    });
}

// Skill bar animations
function initSkillBars() {
    const skillBars = document.querySelectorAll('.skill-progress');
    
    const animateSkillBars = () => {
        skillBars.forEach(bar => {
            const progress = bar.getAttribute('data-progress');
            const rect = bar.getBoundingClientRect();
            
            if (rect.top < window.innerHeight && rect.bottom > 0 && !bar.classList.contains('animated')) {
                setTimeout(() => {
                    bar.style.width = progress + '%';
                    bar.classList.add('animated');
                }, 300);
            }
        });
    };
    
    window.addEventListener('scroll', animateSkillBars);
    animateSkillBars(); // Initial check
}

// Profile shape hover effect
const profileShape = document.querySelector('.profile-shape');
if (profileShape) {
    profileShape.addEventListener('mouseenter', () => {
        profileShape.style.transform = 'scale(1.05) rotate(2deg)';
    });
    
    profileShape.addEventListener('mouseleave', () => {
        profileShape.style.transform = 'scale(1) rotate(0deg)';
    });
}

// CTA button effects
const ctaButtons = document.querySelectorAll('.cta-button');
ctaButtons.forEach(button => {
    button.addEventListener('mouseenter', function() {
        this.style.transform = 'translateY(-2px)';
    });
    
    button.addEventListener('mouseleave', function() {
        this.style.transform = 'translateY(0)';
    });
});

// Work items hover effects
const workItems = document.querySelectorAll('.work-item');
workItems.forEach(item => {
    item.addEventListener('mouseenter', function() {
        this.style.transform = 'translateY(-10px) scale(1.02)';
    });
    
    item.addEventListener('mouseleave', function() {
        this.style.transform = 'translateY(0) scale(1)';
    });
});

// Contact method hover effects
const contactMethods = document.querySelectorAll('.contact-method');
contactMethods.forEach(method => {
    method.addEventListener('mouseenter', function() {
        this.style.transform = 'translateX(15px)';
        this.style.backgroundColor = '#fff';
    });
    
    method.addEventListener('mouseleave', function() {
        this.style.transform = 'translateX(0)';
        this.style.backgroundColor = '';
    });
});

// Timeline animation on scroll
function animateTimeline() {
    const timelineItems = document.querySelectorAll('.timeline-item');
    timelineItems.forEach((item, index) => {
        const rect = item.getBoundingClientRect();
        if (rect.top < window.innerHeight - 100) {
            setTimeout(() => {
                item.style.opacity = '1';
                item.style.transform = 'translateX(0)';
            }, index * 200);
        }
    });
}

// Initialize timeline items as hidden
document.querySelectorAll('.timeline-item').forEach(item => {
    item.style.opacity = '0';
    item.style.transform = 'translateX(-30px)';
    item.style.transition = 'all 0.6s ease';
});

window.addEventListener('scroll', animateTimeline);

// Page loading animation
window.addEventListener('load', () => {
    const loader = document.querySelector('.page-loader');
    if (loader) {
        loader.style.opacity = '0';
        setTimeout(() => {
            loader.style.display = 'none';
        }, 500);
    }
});

// Add intersection observer for better performance
const observerOptions = {
    threshold: 0.1,
    rootMargin: '0px 0px -50px 0px'
};

const observer = new IntersectionObserver((entries) => {
    entries.forEach(entry => {
        if (entry.isIntersecting) {
            entry.target.classList.add('in-view');
        }
    });
}, observerOptions);

// Observe all animatable elements
document.querySelectorAll('.work-item, .skill-group, .timeline-item, .contact-method').forEach(el => {
    observer.observe(el);
});

/*
============================================
DYNAMIC CONTENT MANAGEMENT SYSTEM
============================================

This portfolio uses a dynamic content management system where all content
can be updated through JavaScript without editing the HTML directly.

Dynamic Content IDs and their purposes:

Navigation:
- #dynamic-nav-name: Brand name in side navigation

Intro Section:
- #dynamic-name: Main name/title in intro
- #dynamic-title: Professional title/role
- #dynamic-bio: Brief bio/introduction text

About Section:
- #dynamic-about-content: Main about section content

Projects Section:
- #dynamic-projects-container: Container for all project cards

Skills Section:
- #dynamic-skills-container: Container for all skill groups

Contact Section:
- #dynamic-contact-info: Contact information and details
- #dynamic-social-links: Social media links

Footer:
- #dynamic-footer-name: Name in footer

To update content dynamically, use:
document.getElementById('dynamic-element-id').innerHTML = 'new content';

Example usage:
document.getElementById('dynamic-name').innerHTML = 'John Doe';
document.getElementById('dynamic-title').innerHTML = 'Full Stack Developer';
document.getElementById('dynamic-bio').innerHTML = 'Passionate developer with 5+ years of experience...';

This system allows for:
1. Easy content updates via admin panel
2. Dynamic portfolio generation
3. Multi-language support
4. Content personalization
5. Real-time content management

Note: All dynamic elements are marked with visual indicators in development mode
for easy identification and management.
*/