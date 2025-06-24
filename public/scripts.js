const API_BASE = 'http://localhost:8000';

// API call function
async function apiCall(method, path, data = null, isFile = false){
    const options = {method};
    if(data && !isFile){
        options.headers = {'Content-Type': 'application/json'};
        options.body = JSON.stringify(data);
    } else if(isFile){
        options.body = data;
    }
    const response = await fetch(`${API_BASE}${path}`,options);
    return response.json();
}

// Loading Users
async function loadUsers(){
    const {status, data} = await apiCall('GET', '/users');
    if(status === 'success'){
        const tbody = document.querySelector('#userTable tbody');
        tbody.innerHTML = '';
        data.forEach(user => {
            const row = document.createElement('tr');
            row.innerHTML =
                `<td>${user.user_id}</td>
                <td>${user.first_name} ${user.last_name}</td>
                <td>${user.email}</td>
                <td>${user.role}</td>
                <td>
                <button class="btn btn-sm btn-warning" onclick="editUser(${user.user_id})">Edit</button> 
                <button class="btn btn-sm btn-danger" onclick="deleteUser(${user.user_id})">Delete</button> 
                </td>`;
            tbody.appendChild(row);
        });
    }
}

document.getElementById('userForm')?.addEventListener('submit', async (e) => {
    e.preventDefault();
    const userId = document.getElementById('user_id').value;
    const data = {
        first_name: document.getElementById('first_name').value,
        last_name: document.getElementById('last_name').value,
        email: document.getElementById('email').value,
        password: document.getElementById('password').value,
        phone: document.getElementById('phone').value,
        role: document.getElementById('role').value
    };
    const method = userId ? 'PUT' : 'POST';
    const path = userId ? `/users/${userId}` : '/users';
    const response = await apiCall(method, path, data);
    alert(response.status === 'success' ? 'User saved!' : response.message);
    loadUsers();
    e.target.reset();
    document.getElementById('user_id').value = '';
});

async function editUser(id) {
    const response = await apiCall('GET', `/users/${id}`);
    console.log('Edit User Response', response);
    // const { status, data } = await apiCall('GET', `/users/${id}`);
    if (response.status === 'success' && response.data && Object.keys(response.data).length > 0) {
        const data = response.data;
        console.log('User data: ', data); // only for Debuging
        document.getElementById('user_id').value = data.user_id || '';
        document.getElementById('first_name').value = data.first_name || '';
        document.getElementById('last_name').value = data.last_name || '';
        document.getElementById('email').value = data.email || '';
        document.getElementById('phone').value = data.phone || '';
        document.getElementById('role').value = data.role || '';
    } else {
        console.error('Failed to load user: ', response?.message || 'No Response');
        alert('Failed to load user: ' + response?.message || 'No Response');
    }
}

async function deleteUser(id) {
    if (confirm('Delete this user?')) {
        const response = await apiCall('DELETE', `/users/${id}`);
        alert(response.status === 'success' ? 'User deleted!' : response.message);
        loadUsers();
    }
}

document.getElementById('resumeForm')?.addEventListener('submit', async (e) => {
    e.preventDefault();
    const formData = new FormData();
    formData.append('resume', document.getElementById('resume').files[0]);
    const response = await apiCall('POST', '/upload_resume', formData, true);
    alert(response.status === 'success' ? 'Resume uploaded!' : response.message);
    e.target.reset();
});

// Loading Skills
async function loadSkills() {
    const { status, data } = await apiCall('GET', '/skills');
    if (status === 'success') {
        const tbody = document.querySelector('#skillTable tbody');
        tbody.innerHTML = '';
        data.forEach(skill => {
            const row = document.createElement('tr');
            row.innerHTML = `
                <td>${skill.skill_id}</td>
                <td>${skill.name}</td>
                <td>
                    <button class="btn btn-sm btn-warning" onclick="editSkill(${skill.skill_id})">Edit</button>
                    <button class="btn btn-sm btn-danger" onclick="deleteSkill(${skill.skill_id})">Delete</button>
                </td>
            `;
            tbody.appendChild(row);
        });
        // Populate dropdowns
        const skillSelects = [document.getElementById('skill_id_user'), document.getElementById('skill_id_job')];
        skillSelects.forEach(select => {
            select.innerHTML = '<option value="">Select Skill</option>';
            data.forEach(skill => {
                select.innerHTML += `<option value="${skill.skill_id}">${skill.name}</option>`;
            });
        });
    }
}

document.getElementById('skillForm')?.addEventListener('submit', async (e) => {
    e.preventDefault();
    const skillId = document.getElementById('skill_id').value;
    const data = { name: document.getElementById('name').value };
    const method = skillId ? 'PUT' : 'POST';
    const path = skillId ? `/skills/${skillId}` : '/skills';
    const response = await apiCall(method, path, data);
    alert(response.status === 'success' ? 'Skill saved!' : response.message);
    loadSkills();
    e.target.reset();
    document.getElementById('skill_id').value = '';
});

async function editSkill(id) {
    const { status, data } = await apiCall('GET', `/skills/${id}`);
    if (status === 'success') {
        document.getElementById('skill_id').value = data.skill_id;
        document.getElementById('name').value = data.name;
    }
}

async function deleteSkill(id) {
    if (confirm('Delete this skill?')) {
        const response = await apiCall('DELETE', `/skills/${id}`);
        alert(response.status === 'success' ? 'Skill deleted!' : response.message);
        loadSkills();
    }
}

document.getElementById('userSkillForm')?.addEventListener('submit', async (e) => {
    e.preventDefault();
    const data = {
        user_id: document.getElementById('user_id').value,
        skill_id: document.getElementById('skill_id_user').value
    };
    const response = await apiCall('POST', '/user_skills', data);
    alert(response.status === 'success' ? 'Skill assigned to user!' : response.message);
    e.target.reset();
});

document.getElementById('jobSkillForm')?.addEventListener('submit', async (e) => {
    e.preventDefault();
    const data = {
        job_id: document.getElementById('job_id').value,
        skill_id: document.getElementById('skill_id_job').value
    };
    const response = await apiCall('POST', '/job_skills', data);
    alert(response.status === 'success' ? 'Skill assigned to job!' : response.message);
    e.target.reset();
});

// Loading Companies
async function loadCompanies() {
    const { status, data } = await apiCall('GET', '/companies');
    if (status === 'success') {
        const tbody = document.querySelector('#companyTable tbody');
        tbody.innerHTML = '';
        data.forEach(company => {
            const row = document.createElement('tr');
            row.innerHTML = `
                <td>${company.company_id}</td>
                <td>${company.name}</td>
                <td>${company.description}</td>
                <td>${company.website}</td>
                <td>${company.location || ''}</td>
                <td>
                    <button class="btn btn-sm btn-warning" onclick="editCompany(${company.company_id})">Edit</button>
                    <button class="btn btn-sm btn-danger" onclick="deleteCompany(${company.company_id})">Delete</button>
                </td>
            `;
            tbody.appendChild(row);
        });
    }
}

document.getElementById('companyForm')?.addEventListener('submit', async (e) => {
    e.preventDefault();
    const companyId = document.getElementById('company_id').value;
    const data = {
        name: document.getElementById('name').value,
        description: document.getElementById('description').value,
        location: document.getElementById('location').value,
        website: document.getElementById('website').value
    };
    const method = companyId ? 'PUT' : 'POST';
    const path = companyId ? `/companies/${companyId}` : '/companies';
    const response = await apiCall(method, path, data);
    alert(response.status === 'success' ? 'Company saved!' : response.message);
    loadCompanies();
    e.target.reset();
    document.getElementById('company_id').value = '';
});

async function editCompany(id) {
    const response = await apiCall('GET', `/companies/${id}`);
    console.log('Edit Company Response', response);
    // const { status, data } = await apiCall('GET', `/companies/${id}`);
    if (response.status === 'success' && response.data) {
        const data = response.data;
        document.getElementById('company_id').value = data.company_id || '';
        document.getElementById('name').value = data.name;
        document.getElementById('description').value = data.description || '';
        document.getElementById('location').value = data.location || '';
        document.getElementById('website').value = data.website || '';
        // alert(response.status === 'success' ? 'Company Edited' : response.message);
    } else {
        console.error('Failed to load Company: ', response?.message || 'No Response');
        alert('Failed to load Company: ' + response?.message || 'No Response');
    }
}

async function deleteCompany(id) {
    if (confirm('Delete this company?')) {
        const response = await apiCall('DELETE', `/companies/${id}`);
        alert(response.status === 'success' ? 'Company deleted!' : response.message);
        loadCompanies();
    }
}

// Loading Jobs

async function loadJobs() {
    const { status, data } = await apiCall('GET', '/jobs');
    if (status === 'success') {
        const tbody = document.querySelector('#jobTable tbody');
        if(!tbody){
            console.error('Job table is not found!');
            alert('Check job.html');
            return;
        }
        tbody.innerHTML = '';
        data.forEach(job => {
            const row = document.createElement('tr');
            row.innerHTML = `
                <td>${job.job_id}</td>
                <td>${job.title}</td>
                <td>${job.description}</td>
                <td>${job.company_id}</td>
                <td>${job.location}</td>
                <td>${job.salary}</td>
                <td>${job.posted_date}</td>
                <td>${job.status}</td>
                <td>${job.recruiter_id}</td>
                <td>
                    <button class="btn btn-sm btn-warning" onclick="editJob(${job.job_id})">Edit</button>
                    <button class="btn btn-sm btn-danger" onclick="deleteJob(${job.job_id})">Delete</button>
                </td>
            `;
            tbody.appendChild(row);
        });
    } else {
        console.error('Failed to load job:', data?.message || 'No response' );
        alert('Failed to load job: ' + (data ?.message || 'No response'));
    }
}

document.getElementById('jobForm')?.addEventListener('submit', async (e) => {
    e.preventDefault();
    const jobId = document.getElementById('job_id').value;
    const data = {
        title: document.getElementById('title').value,
        description: document.getElementById('description').value,
        company_id: document.getElementById('company_id').value,
        location: document.getElementById('location').value,
        salary: document.getElementById('salary').value,
        posted_date: document.getElementById('posted_date').value,
        status: document.getElementById('status').value,
        recruiter_id: document.getElementById('recruiter_id').value
    };
    const method = jobId ? 'PUT' : 'POST';
    const path = jobId ? `/jobs/${jobId}` : '/jobs';
    const response = await apiCall(method, path, data);
    alert(response.status === 'success' ? 'Job saved!' : response.message);
    loadJobs();
    e.target.reset();
    document.getElementById('job_id').value = '';
});

async function editJob(id) {
    const response = await apiCall('GET', `/jobs/${id}`);
    console.log('Edit Job Response', response);
    if (response.status === 'success' && response.data) {
        const data = response.data;
        document.getElementById('job_id').value = data.job_id || '';
        document.getElementById('title').value = data.title;
        document.getElementById('description').value = data.description || '';
        document.getElementById('company_id').value = data.company_id || '';
        document.getElementById('location').value = data.location || '';
        document.getElementById('salary').value = data.salary || '';
        document.getElementById('posted_date').value = data.posted_date || '';
        document.getElementById('status').value = data.status || '';
        document.getElementById('recruiter_id').value = data.recruiter_id || '';

    }
}

async function deleteJob(id) {
    if (confirm('Delete this Job?')) {
        const response = await apiCall('DELETE', `/jobs/${id}`);
        alert(response.status === 'success' ? 'Job deleted!' : response.message);
        loadJobs();
    }
}

// Loading Application

// Load jobs for dropdown
async function loadJobsForDropdown() {
    const { status, data } = await apiCall('GET', '/jobs');
    if (status === 'success') {
        const select = document.getElementById('job_id');
        if (!select) {
            console.error('Job dropdown not found');
            return;
        }
        select.innerHTML = '<option value="">Select Job (Company ID)</option>';
        data.forEach(job => {
            select.innerHTML += `<option value="${job.job_id}">${job.title} (${job.company_id})</option>`;
        });
    } else {
        console.error('Failed to load jobs:', data?.message || 'No response');
        alert('Failed to load jobs: ' + (data?.message || 'No response'));
    }
}

// Load applications for a job and applicant count
async function loadApplications(jobId) {
    // Get applicant count
    const countResponse = await apiCall('GET', `/applications/count?job_id=${jobId}`);
    const countDiv = document.getElementById('applicationCount');
    if(!countDiv){
        console.error('Application count div not found');
        alert('Application count div not found. check applicaition.html');
    }
    if (countResponse.status === 'success') {
        countDiv.innerHTML = `<strong>Applicants:</strong> ${countResponse.data.count}`;
    } else {
        countDiv.innerHTML = `<strong>Error:</strong> ${countResponse.message}`;
    }

    // Get applications
    const { status, data } = await apiCall('GET', `/applications?job_id=${jobId}`);
    const tbody = document.querySelector('#applicationTable tbody');
    const saveButton = document.getElementById('saveStatus');
    if (!tbody || !saveButton) {
        console.error('Application table or save button not found');
        return;
    }
    tbody.innerHTML = '';
    if (status === 'success' && data.length > 0) {
        data.forEach(app => {
            const row = document.createElement('tr');
            row.innerHTML = `
                <td>${app.application_id}</td>
                <td>${app.user_name}</td>
                <td>${app.job_title}</td>
                <td>
                    <select class="form-control status-select" data-id="${app.application_id}">
                        <option value="Submitted" ${app.status === 'Submitted' ? 'selected' : ''}>Submitted</option>
                        <option value="Reviewed" ${app.status === 'Reviewed' ? 'selected' : ''}>Reviewed</option>
                        <option value="Accepted" ${app.status === 'Accepted' ? 'selected' : ''}>Accepted</option>
                        <option value="Rejected" ${app.status === 'Rejected' ? 'selected' : ''}>Rejected</option>
                    </select>
                </td>
            `;
            tbody.appendChild(row);
        });
        saveButton.style.display = 'block';
    } else {
        tbody.innerHTML = '<tr><td colspan="5">No applications found.</td></tr>';
        saveButton.style.display = 'none';
    }
}

// Handle application form submission
document.getElementById('applicationForm')?.addEventListener('submit', async (e) => {
    e.preventDefault();
    const jobId = document.getElementById('job_id').value;
    if (!jobId) {
        alert('Please select a job.');
        return;
    }
    await loadApplications(jobId);
});

// Handle status updates
document.getElementById('statusForm')?.addEventListener('submit', async (e) => {
    e.preventDefault();
    const selects = document.querySelectorAll('.status-select');
    const updates = [];
    selects.forEach(select => {
        updates.push({
            application_id: select.dataset.id,
            status: select.value
        });
    });
    for (const update of updates) {
        const response = await apiCall('PUT', `/applications/${update.application_id}`, { status: update.status });
        if (response.status !== 'success') {
            alert(`Failed to update application ${update.application_id}: ${response.message}`);
            return;
        }
    }
    alert('Application statuses updated successfully!');
    const jobId = document.getElementById('job_id').value;
    if (jobId) await loadApplications(jobId);
});

// Initialize
document.addEventListener('DOMContentLoaded', () => {
    if (document.getElementById('userTable')) loadUsers();
    if (document.getElementById('skillTable')) loadSkills();
    if (document.getElementById('job_id')) loadJobsForDropdown();
});

// Load data on page load
if (document.getElementById('userTable')) loadUsers();
if (document.getElementById('skillTable')) loadSkills();
if (document.getElementById('companyTable')) loadCompanies();
if (document.getElementById('jobTable')) loadJobs();
// if (document.getElementById('applicationTable')) loadApplications();
