// Dynamic Portfolio Content Loader
class PortfolioLoader {
    constructor() {
        console.log('PortfolioLoader constructor called');
        this.skillsContainer = document.getElementById('skills-grid');
        this.projectsContainer = document.getElementById('projects-grid');
        
        console.log('Skills container:', this.skillsContainer);
        console.log('Projects container:', this.projectsContainer);
        
        if (!this.skillsContainer) {
            console.error('Skills container not found!');
            return;
        }
        if (!this.projectsContainer) {
            console.error('Projects container not found!');
            return;
        }
        
        this.loadContent();
    }

    async loadContent() {
        // Load skills and projects simultaneously
        await Promise.all([
            this.loadSkills(),
            this.loadProjects()
        ]);
    }

    async loadSkills() {
        try {
            console.log('Loading skills...');
            const timestamp = Date.now();
            const url = `api/skills.php?cachebust=${timestamp}&v=${Math.random()}`;
            console.log('Fetching skills from:', url);
            
            const response = await fetch(url, {
                method: 'GET',
                headers: {
                    'Accept': 'application/json',
                    'Cache-Control': 'no-cache, no-store, must-revalidate',
                    'Pragma': 'no-cache',
                    'Expires': '0'
                }
            });
            console.log('Skills response status:', response.status);
            console.log('Skills response content-type:', response.headers.get('content-type'));
            
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            
            const result = await response.json();
            console.log('Skills result:', result);
            
            if (result.success) {
                this.renderSkills(result.data);
            } else {
                console.error('Skills API error:', result.error);
                this.showError(this.skillsContainer, 'Failed to load skills: ' + (result.error || 'Unknown error'));
            }
        } catch (error) {
            console.error('Error loading skills:', error);
            this.showError(this.skillsContainer, 'Failed to load skills: ' + error.message);
        }
    }

    async loadProjects() {
        try {
            console.log('Loading projects...');
            const url = `api/projects.php?t=${Date.now()}`;
            const response = await fetch(url, {
                method: 'GET',
                headers: {
                    'Accept': 'application/json',
                    'Cache-Control': 'no-cache'
                }
            });
            console.log('Projects response status:', response.status);
            console.log('Projects response content-type:', response.headers.get('content-type'));
            
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            
            const result = await response.json();
            console.log('Projects result:', result);
            
            if (result.success) {
                this.renderProjects(result.data);
            } else {
                console.error('Projects API error:', result.error);
                this.showError(this.projectsContainer, 'Failed to load projects: ' + (result.error || 'Unknown error'));
            }
        } catch (error) {
            console.error('Error loading projects:', error);
            this.showError(this.projectsContainer, 'Failed to load projects: ' + error.message);
        }
    }

    // Delete functions for admin use
    async deleteSkill(id) {
        try {
            const response = await fetch(`api/manage_skills.php?id=${id}`, {
                method: 'DELETE'
            });
            const result = await response.json();
            
            if (result.success) {
                this.loadSkills(); // Reload skills
                return { success: true, message: result.message };
            } else {
                return { success: false, error: result.error };
            }
        } catch (error) {
            console.error('Error deleting skill:', error);
            return { success: false, error: 'Failed to delete skill' };
        }
    }

    async deleteProject(id) {
        try {
            const response = await fetch(`api/manage_projects.php?id=${id}`, {
                method: 'DELETE'
            });
            const result = await response.json();
            
            if (result.success) {
                this.loadProjects(); // Reload projects
                return { success: true, message: result.message };
            } else {
                return { success: false, error: result.error };
            }
        } catch (error) {
            console.error('Error deleting project:', error);
            return { success: false, error: 'Failed to delete project' };
        }
    }

    renderSkills(skillsData) {
        this.skillsContainer.innerHTML = '';
        
        // Define icons for each category
        const categoryIcons = {
            'Frontend': '🌐',
            'Backend': '⚙️',
            'Database': '🗄️',
            'Programming': '💻',
            'Tools': '🛠️',
            'Other': '📚'
        };

        Object.keys(skillsData).forEach(category => {
            const skills = skillsData[category];
            const icon = categoryIcons[category] || '💡';
            
            const skillElement = document.createElement('div');
            skillElement.className = 'skill-item';
            
            // Create skills list
            const skillNames = skills.map(skill => skill.name).join(', ');
            
            skillElement.innerHTML = `
                <div class="skill-icon">${icon}</div>
                <h4>${category}</h4>
                <p>${skillNames}</p>
            `;
            
            this.skillsContainer.appendChild(skillElement);
        });
    }

    renderProjects(projects) {
        this.projectsContainer.innerHTML = '';
        
        if (projects.length === 0) {
            this.projectsContainer.innerHTML = '<p class="no-data">No featured projects found.</p>';
            return;
        }
        
        projects.forEach(project => {
            const projectElement = document.createElement('div');
            projectElement.className = 'project-card';
            
            // Parse technologies if it's a JSON string
            let technologies = [];
            try {
                technologies = typeof project.technologies === 'string' ? 
                    project.technologies.split(',').map(tech => tech.trim()) : 
                    project.technologies || [];
            } catch (e) {
                technologies = [];
            }
            
            // Create tech tags
            const techTags = technologies.map(tech => 
                `<span class="tech-tag">${tech}</span>`
            ).join('');
            
            projectElement.innerHTML = `
                <div class="project-header">
                    <h4>${this.escapeHtml(project.title)}</h4>
                    <div class="project-links">
                        ${project.github_url ? `<a href="${project.github_url}" target="_blank" class="project-link">GitHub</a>` : ''}
                        ${project.project_url ? `<a href="${project.project_url}" target="_blank" class="project-link">Live Demo</a>` : ''}
                    </div>
                </div>
                <p>${this.escapeHtml(project.description || 'No description available.')}</p>
                <div class="project-tech">
                    ${techTags}
                </div>
            `;
            
            this.projectsContainer.appendChild(projectElement);
        });
    }

    showError(container, message) {
        console.log('showError called with:', container, message);
        if (container) {
            container.innerHTML = `<div class="error-message">${message}</div>`;
        } else {
            console.error('Container is null in showError!');
        }
    }

    escapeHtml(text) {
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }
}

// Initialize when DOM is loaded
document.addEventListener('DOMContentLoaded', () => {
    // Create global instance for admin panel access
    window.portfolioLoader = new PortfolioLoader();
});
