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
    contactForm.addEventListener('submit', async function(e) {
        e.preventDefault();
        
        const submitButton = this.querySelector('.submit-button');
        const originalText = submitButton.textContent;
        
        // Show loading state
        submitButton.textContent = 'Sending...';
        submitButton.disabled = true;
        
        try {
            // Get form data
            const formData = new FormData(this);
            
            // Send form data to PHP handler
            const response = await fetch('contact-simple.php', {
                method: 'POST',
                body: formData
            });
            
            const result = await response.json();
            
            if (result.success) {
                // Success message
                alert(result.message);
                this.reset(); // Clear form
            } else {
                // Error message
                let errorMessage = result.message;
                if (result.errors && result.errors.length > 0) {
                    errorMessage += '\n\n' + result.errors.join('\n');
                }
                alert(errorMessage);
            }
        } catch (error) {
            console.error('Form submission error:', error);
            alert('Sorry, there was an error sending your message. Please try again later.');
        } finally {
            // Restore button state
            submitButton.textContent = originalText;
            submitButton.disabled = false;
        }
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
        profileShape.style.transform = 'scale(1.05)';
    });
    
    profileShape.addEventListener('mouseleave', () => {
        profileShape.style.transform = 'scale(1)';
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

// Dynamic Content Loading
async function loadPortfolioData() {
    try {
        console.log('Loading portfolio data...');
        const response = await fetch('api/portfolio-data.php');
        
        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }
        
        const data = await response.json();
        console.log('Portfolio data received:', data);
        
        if (data.success) {
            populatePersonalInfo(data.personal);
            populateSkills(data.skills);
            populateProjects(data.projects);
            populateSocialLinks(data.social);
            console.log('Portfolio data populated successfully');
        } else {
            console.error('API returned error:', data.error);
            showFallbackContent();
        }
    } catch (error) {
        console.error('Error loading portfolio data:', error);
        showFallbackContent();
    }
}

function showFallbackContent() {
    // Show default content if API fails
    const nameEl = document.getElementById('dynamic-name');
    const titleEl = document.getElementById('dynamic-title');
    const bioEl = document.getElementById('dynamic-bio');
    
    if (nameEl) nameEl.textContent = 'Portfolio Owner';
    if (titleEl) titleEl.textContent = 'Full Stack Developer';
    if (bioEl) bioEl.textContent = 'Welcome to my portfolio. Please check back later for updated content.';
}

function populatePersonalInfo(personal) {
    if (!personal) return;
    
    // Update intro section
    const nameEl = document.getElementById('dynamic-name');
    const titleEl = document.getElementById('dynamic-title');
    const bioEl = document.getElementById('dynamic-bio');
    
    if (nameEl) nameEl.textContent = personal.name || '[Your Name]';
    if (titleEl) titleEl.textContent = personal.title || '[Your Title]';
    if (bioEl) bioEl.textContent = personal.bio || '[Your Bio Description]';
    
    // Update profile image
    const profileContainer = document.getElementById('dynamic-profile-image');
    if (profileContainer) {
        const profileShape = profileContainer.querySelector('.profile-shape');
        if (profileShape) {
            if (personal.profile_image && personal.profile_image.trim() !== '') {
                profileShape.innerHTML = `<img src="${personal.profile_image}" alt="Profile" style="width: 100%; height: 100%; object-fit: cover; object-position: center; border-radius: 50%;">`;
            } else {
                profileShape.innerHTML = '<span style="color: #ffffff; text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.8); font-size: 2.5rem; font-weight: bold;">[IMG]</span>';
            }
        }
    }
    
    // Update about section
    const aboutEl = document.getElementById('dynamic-about');
    if (aboutEl) aboutEl.innerHTML = personal.about || '[About description will appear here]';
    
    // Update contact info
    const emailEl = document.getElementById('dynamic-email');
    const phoneEl = document.getElementById('dynamic-phone');
    const locationEl = document.getElementById('dynamic-location');
    
    if (emailEl) emailEl.textContent = personal.email || '[email]';
    if (phoneEl) phoneEl.textContent = personal.phone || '[phone]';
    if (locationEl) locationEl.textContent = personal.location || '[location]';
    
    // Update footer
    const footerNameEl = document.getElementById('dynamic-footer-name');
    if (footerNameEl) footerNameEl.textContent = personal.name || '[Name]';
}

function populateSkills(skills) {
    const skillsContainer = document.getElementById('dynamic-skills');
    if (!skillsContainer || !skills) return;
    
    // Icon mapping for different skill categories and specific skills
    const skillIcons = {
        // Category icons
        'Frontend': 'fas fa-laptop-code',
        'Backend': 'fas fa-server',
        'Database': 'fas fa-database',
        'Tools': 'fas fa-tools',
        'Other': 'fas fa-cogs',
        
        // Specific skill icons
        'JavaScript': 'fab fa-js-square',
        'React': 'fab fa-react',
        'Vue': 'fab fa-vuejs',
        'Angular': 'fab fa-angular',
        'Node.js': 'fab fa-node-js',
        'Python': 'fab fa-python',
        'PHP': 'fab fa-php',
        'Java': 'fab fa-java',
        'CSS': 'fab fa-css3-alt',
        'HTML': 'fab fa-html5',
        'Sass': 'fab fa-sass',
        'Bootstrap': 'fab fa-bootstrap',
        'Git': 'fab fa-git-alt',
        'GitHub': 'fab fa-github',
        'Docker': 'fab fa-docker',
        'AWS': 'fab fa-aws',
        'MySQL': 'fas fa-database',
        'MongoDB': 'fas fa-leaf',
        'PostgreSQL': 'fas fa-database'
    };
    
    function getSkillIcon(skillName, category) {
        return skillIcons[skillName] || skillIcons[category] || 'fas fa-code';
    }
    
    let skillsHTML = '';
    for (const [category, categorySkills] of Object.entries(skills)) {
        const categoryIcon = skillIcons[category] || 'fas fa-code';
        skillsHTML += `
            <div class="skill-category">
                <div class="skill-category-header">
                    <i class="${categoryIcon}"></i>
                    <h3 class="skill-category-title">${category}</h3>
                </div>
                <div class="skills-grid">
        `;
        
        categorySkills.forEach(skill => {
            const skillIcon = getSkillIcon(skill.skill_name, category);
            skillsHTML += `
                <div class="skill-item">
                    <div class="skill-header">
                        <div class="skill-icon">
                            <i class="${skillIcon}"></i>
                        </div>
                        <div class="skill-info">
                            <span class="skill-name">${skill.skill_name}</span>
                            <span class="skill-percent">${skill.proficiency}%</span>
                        </div>
                    </div>
                    <div class="skill-bar">
                        <div class="skill-progress" style="width: ${skill.proficiency}%"></div>
                    </div>
                </div>
            `;
        });
        
        skillsHTML += `
                </div>
            </div>
        `;
    }
    
    skillsContainer.innerHTML = skillsHTML;
}

function populateProjects(projects) {
    const projectsContainer = document.getElementById('dynamic-projects');
    if (!projectsContainer || !projects) return;
    
    if (projects.length === 0) {
        projectsContainer.innerHTML = '<p class="no-projects">No projects available yet.</p>';
        return;
    }
    
    let projectsHTML = '';
    projects.forEach(project => {
        const technologies = JSON.parse(project.technologies || '[]');
        const techTags = technologies.map(tech => `<span class="tech-tag">${tech}</span>`).join('');
        
        projectsHTML += `
            <div class="project-card">
                <div class="project-content">
                    <h3 class="project-title">${project.title}</h3>
                    <p class="project-description">${project.description}</p>
                    <div class="project-tech">
                        ${techTags}
                    </div>
                    <div class="project-links">
                        ${project.github_url ? 
                            `<a href="${project.github_url}" class="btn btn-secondary" target="_blank" rel="noopener noreferrer">
                                <i class="fab fa-github"></i> Code
                            </a>` : ''
                        }
                    </div>
                </div>
            </div>
        `;
    });
    
    projectsContainer.innerHTML = projectsHTML;
}

function populateSocialLinks(socialLinks) {
    const socialContainer = document.getElementById('dynamic-social-links');
    if (!socialContainer || !socialLinks) return;
    
    let socialHTML = '';
    socialLinks.forEach(social => {
        socialHTML += `
            <a href="${social.url}" class="social-link" target="_blank" rel="noopener noreferrer" title="${social.platform}">
                <i class="${social.icon_class}"></i>
            </a>
        `;
    });
    
    socialContainer.innerHTML = socialHTML;
}

// Load data when page loads
document.addEventListener('DOMContentLoaded', loadPortfolioData);