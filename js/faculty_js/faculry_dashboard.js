// Research Teacher Dashboard JavaScript
document.addEventListener('DOMContentLoaded', function() {
    initializeDashboard();
    loadDashboardData();
    showSection('dashboard'); // Show dashboard by default
});

// Data storage - would typically be loaded from API
let groupsData = [];
let documentsData = [];
let notificationsData = [];
let progressData = [];
let commentsData = [];
let currentUser = null;

// Initialize dashboard functionality
function initializeDashboard() {
    // Set up navigation event listeners
    document.querySelectorAll('.nav-item').forEach(item => {
        item.addEventListener('click', function(e) {
            e.preventDefault();
            // Remove active class from all nav items
            document.querySelectorAll('.nav-item').forEach(nav => {
                nav.classList.remove('bg-royal-blue-light');
            });
            // Add active class to clicked item
            this.classList.add('bg-royal-blue-light');
        });
    });

    // Set up form submission
    const addGroupForm = document.getElementById('addGroupForm');
    if (addGroupForm) {
        addGroupForm.addEventListener('submit', handleAddGroup);
    }
}

async function loadDashboardData() {
    try {
        await loadGroups();
        await loadDocuments();
        await loadNotifications();
        await loadProgressTracking();
        updateDashboardStats();
    } catch (error) {
        console.error('Error loading dashboard data:', error);
        showNotification('Error loading dashboard data', 'error');
    }
}

// Simulated API calls - replace with actual fetch calls
async function loadGroups() {
    // This would be: const response = await fetch('/api/groups');
    groupsData = [
        {
            id: 1,
            name: 'AI Research Team',
            description: 'Developing machine learning solutions for educational platforms',
            adviser_id: 2,
            adviser_name: 'Dr. Sarah Johnson',
            status: 'active',
            created_at: '2024-08-15T08:00:00Z',
            updated_at: '2024-09-10T14:30:00Z',
            members: [
                { id: 10, name: 'John Doe', email: 'john@student.edu', role: 'leader' },
                { id: 11, name: 'Jane Smith', email: 'jane@student.edu', role: 'member' },
                { id: 12, name: 'Bob Johnson', email: 'bob@student.edu', role: 'member' }
            ],
            current_document: 'AI-Powered Learning Management System'
        },
        {
            id: 2,
            name: 'Blockchain Innovators',
            description: 'Exploring blockchain applications in academic record management',
            adviser_id: 3,
            adviser_name: 'Prof. Michael Chen',
            status: 'active',
            created_at: '2024-07-20T10:00:00Z',
            updated_at: '2024-09-08T16:45:00Z',
            members: [
                { id: 13, name: 'Alice Brown', email: 'alice@student.edu', role: 'leader' },
                { id: 14, name: 'Charlie Wilson', email: 'charlie@student.edu', role: 'member' }
            ],
            current_document: 'Blockchain-Based Student Records System'
        },
        {
            id: 3,
            name: 'Mobile Solutions',
            description: 'Creating mobile applications for campus services',
            adviser_id: 2,
            adviser_name: 'Dr. Sarah Johnson',
            status: 'completed',
            created_at: '2024-06-01T09:00:00Z',
            updated_at: '2024-09-01T12:00:00Z',
            members: [
                { id: 15, name: 'Eva Davis', email: 'eva@student.edu', role: 'leader' },
                { id: 16, name: 'Frank Miller', email: 'frank@student.edu', role: 'member' },
                { id: 17, name: 'Grace Lee', email: 'grace@student.edu', role: 'member' }
            ],
            current_document: 'Campus Navigation Mobile App'
        }
    ];
}

async function loadDocuments() {
    documentsData = [
        {
            id: 1,
            group_id: 1,
            group_name: 'AI Research Team',
            title: 'AI-Powered Learning Management System - Proposal',
            type: 'proposal',
            file_path: '/uploads/documents/ai_lms_proposal_v1.pdf',
            file_size: 2048576,
            mime_type: 'application/pdf',
            status: 'under_review',
            submitted_by: 10,
            submitted_by_name: 'John Doe',
            submitted_at: '2024-09-10T14:30:00Z',
            created_at: '2024-09-10T14:30:00Z',
            updated_at: '2024-09-10T14:30:00Z'
        },
        {
            id: 2,
            group_id: 2,
            group_name: 'Blockchain Innovators',
            title: 'Blockchain Records System - Chapter 1',
            type: 'chapter1',
            file_path: '/uploads/documents/blockchain_chapter1_v2.pdf',
            file_size: 3145728,
            mime_type: 'application/pdf',
            status: 'revision_needed',
            submitted_by: 13,
            submitted_by_name: 'Alice Brown',
            submitted_at: '2024-09-08T16:45:00Z',
            created_at: '2024-09-08T16:45:00Z',
            updated_at: '2024-09-09T10:20:00Z'
        },
        {
            id: 3,
            group_id: 3,
            group_name: 'Mobile Solutions',
            title: 'Campus Navigation App - Final Document',
            type: 'final',
            file_path: '/uploads/documents/campus_nav_final.pdf',
            file_size: 5242880,
            mime_type: 'application/pdf',
            status: 'approved',
            submitted_by: 15,
            submitted_by_name: 'Eva Davis',
            submitted_at: '2024-09-01T12:00:00Z',
            created_at: '2024-09-01T12:00:00Z',
            updated_at: '2024-09-01T12:00:00Z'
        }
    ];
}

async function loadNotifications() {
    notificationsData = [
        {
            id: 1,
            title: 'New Document Submission',
            message: 'AI Research Team submitted their proposal for review',
            type: 'info',
            is_read: false,
            related_type: 'document',
            related_id: 1,
            created_at: '2024-09-10T14:30:00Z'
        },
        {
            id: 2,
            title: 'Review Completed',
            message: 'Campus Navigation App final document has been approved',
            type: 'success',
            is_read: false,
            related_type: 'document',
            related_id: 3,
            created_at: '2024-09-01T12:30:00Z'
        },
        {
            id: 3,
            title: 'Revision Required',
            message: 'Blockchain Records System Chapter 1 needs revision',
            type: 'warning',
            related_type: 'document',
            related_id: 2,
            created_at: '2024-09-09T10:20:00Z'
        }
    ];
}

async function loadProgressTracking() {
    progressData = [
        {
            id: 1,
            group_id: 1,
            group_name: 'AI Research Team',
            milestone: 'Proposal Submission',
            description: 'Submit research proposal for approval',
            target_date: '2024-09-15',
            completion_date: '2024-09-10',
            status: 'completed',
            created_at: '2024-08-15T08:00:00Z'
        },
        {
            id: 2,
            group_id: 1,
            group_name: 'AI Research Team',
            milestone: 'Chapter 1 Draft',
            description: 'Complete first chapter draft',
            target_date: '2024-10-01',
            completion_date: null,
            status: 'in_progress',
            created_at: '2024-09-11T09:00:00Z'
        },
        {
            id: 3,
            group_id: 2,
            group_name: 'Blockchain Innovators',
            milestone: 'Literature Review',
            description: 'Complete comprehensive literature review',
            target_date: '2024-09-20',
            completion_date: null,
            status: 'overdue',
            created_at: '2024-08-20T10:00:00Z'
        }
    ];
}

// Navigation functions
function showSection(sectionName) {
    // Hide all sections
    document.querySelectorAll('.section').forEach(section => {
        section.classList.add('hidden');
    });
    
    // Show the selected section
    const targetSection = document.getElementById(`${sectionName}-section`);
    if (targetSection) {
        targetSection.classList.remove('hidden');
    }
    
    // Load section-specific data
    switch(sectionName) {
        case 'groups':
            populateGroupsTable();
            break;
        case 'submissions':
            populateSubmissionsTable();
            break;
        case 'reviews':
            populateReviewsSection();
            break;
    }
}

// Group management functions
function populateGroupsTable() {
    const tableBody = document.getElementById('groupsTableBody');
    if (!tableBody) return;
    
    tableBody.innerHTML = '';
    
    groupsData.forEach(group => {
        const row = document.createElement('tr');
        row.className = 'hover:bg-gray-50';
        
        const statusClass = getStatusClass(group.status);
        const statusText = group.status.charAt(0).toUpperCase() + group.status.slice(1).replace('_', ' ');
        
        row.innerHTML = `
            <td class="table-cell font-medium">GRP${String(group.id).padStart(3, '0')}</td>
            <td class="table-cell">${group.name}</td>
            <td class="table-cell">
                <div class="text-sm space-y-1">
                    ${group.members.map(member => 
                        `<div class="flex items-center justify-between">
                            <span class="font-medium">${member.name}</span>
                            <span class="text-xs px-2 py-1 rounded ${member.role === 'leader' ? 'bg-blue-100 text-blue-800' : 'bg-gray-100 text-gray-600'}">${member.role}</span>
                        </div>
                        <div class="text-xs text-gray-500">${member.email}</div>`
                    ).join('')}
                </div>
            </td>
            <td class="table-cell">${group.current_document || 'No active document'}</td>
            <td class="table-cell">
                <span class="px-2 py-1 rounded-full text-xs font-semibold ${statusClass}">
                    ${statusText}
                </span>
            </td>
            <td class="table-cell">${formatDate(group.created_at)}</td>
            <td class="table-cell">
                <div class="flex space-x-2">
                    <button onclick="viewGroup(${group.id})" class="text-blue-600 hover:text-blue-800 text-sm font-medium">View</button>
                    <button onclick="editGroup(${group.id})" class="text-green-600 hover:text-green-800 text-sm font-medium">Edit</button>
                    <button onclick="manageProgress(${group.id})" class="text-purple-600 hover:text-purple-800 text-sm font-medium">Progress</button>
                </div>
            </td>
        `;
        
        tableBody.appendChild(row);
    });
}

// Document submissions management
function populateSubmissionsTable() {
    const tableBody = document.getElementById('submissionsTableBody');
    if (!tableBody) return;
    
    tableBody.innerHTML = '';
    
    documentsData.forEach(document => {
        const row = document.createElement('tr');
        row.className = 'hover:bg-gray-50';
        
        const statusClass = getDocumentStatusClass(document.status);
        const statusText = document.status.replace('_', ' ').replace(/\b\w/g, l => l.toUpperCase());
        const fileSize = formatFileSize(document.file_size);
        
        row.innerHTML = `
            <td class="table-cell font-medium">DOC${String(document.id).padStart(3, '0')}</td>
            <td class="table-cell">${document.group_name}</td>
            <td class="table-cell">
                <div class="font-medium">${document.title}</div>
                <div class="text-xs text-gray-500">Type: ${document.type.charAt(0).toUpperCase() + document.type.slice(1)} • Size: ${fileSize}</div>
            </td>
            <td class="table-cell">${formatDate(document.submitted_at)}</td>
            <td class="table-cell">
                <span class="px-2 py-1 rounded-full text-xs font-semibold ${statusClass}">
                    ${statusText}
                </span>
            </td>
            <td class="table-cell">
                <div class="flex space-x-2">
                    <button onclick="reviewDocument(${document.id})" class="text-blue-600 hover:text-blue-800 text-sm font-medium">Review</button>
                    <button onclick="downloadDocument(${document.id})" class="text-green-600 hover:text-green-800 text-sm font-medium">Download</button>
                    <button onclick="viewComments(${document.id})" class="text-purple-600 hover:text-purple-800 text-sm font-medium">Comments</button>
                    ${document.status === 'under_review' ? 
                        `<button onclick="approveDocument(${document.id})" class="text-emerald-600 hover:text-emerald-800 text-sm font-medium">Approve</button>
                         <button onclick="requestRevision(${document.id})" class="text-red-600 hover:text-red-800 text-sm font-medium">Revise</button>` : ''
                    }
                </div>
            </td>
        `;
        
        tableBody.appendChild(row);
    });
}

// Reviews and feedback functions
function populateReviewsSection() {
    populatePendingReviews();
    populateRecentFeedback();
}

function populatePendingReviews() {
    const container = document.getElementById('pendingReviewsList');
    if (!container) return;
    
    container.innerHTML = '';
    
    const pendingDocs = documentsData.filter(doc => doc.status === 'under_review');
    
    pendingDocs.forEach(document => {
        const reviewCard = document.createElement('div');
        reviewCard.className = 'p-4 border border-gray-200 rounded-lg hover:bg-gray-50';
        
        const daysAgo = Math.floor((new Date() - new Date(document.submitted_at)) / (1000 * 60 * 60 * 24));
        const urgencyClass = daysAgo > 3 ? 'text-red-600' : daysAgo > 1 ? 'text-yellow-600' : 'text-green-600';
        
        reviewCard.innerHTML = `
            <div class="flex justify-between items-start mb-2">
                <h3 class="font-semibold text-gray-900">${document.group_name}</h3>
                <span class="text-xs ${urgencyClass} font-semibold">${daysAgo} days ago</span>
            </div>
            <p class="text-sm text-gray-600 mb-2">${document.title}</p>
            <div class="text-xs text-gray-500 mb-3">
                Submitted by: ${document.submitted_by_name} • Type: ${document.type}
            </div>
            <div class="flex justify-between items-center">
                <span class="text-xs text-gray-500">Submitted: ${formatDate(document.submitted_at)}</span>
                <div class="space-x-2">
                    <button onclick="reviewDocument(${document.id})" class="text-blue-600 hover:text-blue-800 text-sm font-medium">
                        Start Review
                    </button>
                    <button onclick="downloadDocument(${document.id})" class="text-green-600 hover:text-green-800 text-sm font-medium">
                        Download
                    </button>
                </div>
            </div>
        `;
        
        container.appendChild(reviewCard);
    });
    
    if (pendingDocs.length === 0) {
        container.innerHTML = '<p class="text-gray-500 text-center py-8">No pending reviews</p>';
    }
}

function populateRecentFeedback() {
    const container = document.getElementById('recentFeedbackList');
    if (!container) return;
    
    container.innerHTML = '';
    
    // Get recent documents with feedback status
    const recentFeedback = documentsData
        .filter(doc => ['approved', 'revision_needed'].includes(doc.status))
        .sort((a, b) => new Date(b.updated_at) - new Date(a.updated_at))
        .slice(0, 5);
    
    recentFeedback.forEach(document => {
        const feedbackCard = document.createElement('div');
        feedbackCard.className = 'p-4 border border-gray-200 rounded-lg hover:bg-gray-50';
        
        const statusClass = document.status === 'approved' ? 'text-green-600' : 'text-red-600';
        const statusText = document.status === 'approved' ? 'APPROVED' : 'REVISION NEEDED';
        
        feedbackCard.innerHTML = `
            <div class="flex justify-between items-start mb-2">
                <h3 class="font-semibold text-gray-900">${document.group_name}</h3>
                <span class="text-xs ${statusClass} font-semibold">${statusText}</span>
            </div>
            <p class="text-sm text-gray-600 mb-2">${document.title}</p>
            <div class="flex justify-between items-center">
                <span class="text-xs text-gray-500">Updated: ${formatDate(document.updated_at)}</span>
                <button onclick="viewComments(${document.id})" class="text-purple-600 hover:text-purple-800 text-sm font-medium">
                    View Comments
                </button>
            </div>
        `;
        
        container.appendChild(feedbackCard);
    });
    
    if (recentFeedback.length === 0) {
        container.innerHTML = '<p class="text-gray-500 text-center py-8">No recent feedback</p>';
    }
}

// Modal functions
function showAddGroupModal() {
    document.getElementById('addGroupModal').classList.remove('hidden');
    document.getElementById('addGroupModal').classList.add('flex');
    document.body.style.overflow = 'hidden';
}

function hideAddGroupModal() {
    document.getElementById('addGroupModal').classList.add('hidden');
    document.getElementById('addGroupModal').classList.remove('flex');
    document.body.style.overflow = 'auto';
    
    // Reset form
    document.getElementById('addGroupForm').reset();
    resetMemberFields();
}

function addMemberField() {
    const container = document.getElementById('membersContainer');
    const memberDiv = document.createElement('div');
    memberDiv.className = 'flex gap-2 mb-2 member-input';
    memberDiv.innerHTML = `
        <input type="text" placeholder="Student Name" required class="flex-1 border border-gray-300 rounded-md px-3 py-2">
        <input type="email" placeholder="Student Email" required class="flex-1 border border-gray-300 rounded-md px-3 py-2">
        <select class="border border-gray-300 rounded-md px-3 py-2">
            <option value="member">Member</option>
            <option value="leader">Leader</option>
        </select>
        <button type="button" onclick="removeMember(this)" class="px-3 py-2 bg-red-500 text-white rounded-md hover:bg-red-600">Remove</button>
    `;
    container.appendChild(memberDiv);
}

function removeMember(button) {
    const memberInputs = document.querySelectorAll('.member-input');
    if (memberInputs.length > 1) {
        button.closest('.member-input').remove();
    } else {
        alert('At least one member is required.');
    }
}

function resetMemberFields() {
    const container = document.getElementById('membersContainer');
    container.innerHTML = `
        <div class="flex gap-2 mb-2 member-input">
            <input type="text" placeholder="Student Name" required class="flex-1 border border-gray-300 rounded-md px-3 py-2">
            <input type="email" placeholder="Student Email" required class="flex-1 border border-gray-300 rounded-md px-3 py-2">
            <select class="border border-gray-300 rounded-md px-3 py-2">
                <option value="member">Member</option>
                <option value="leader">Leader</option>
            </select>
            <button type="button" onclick="removeMember(this)" class="px-3 py-2 bg-red-500 text-white rounded-md hover:bg-red-600">Remove</button>
        </div>
    `;
}

// Form handling
function handleAddGroup(e) {
    e.preventDefault();
    
    const memberInputs = document.querySelectorAll('.member-input');
    const members = [];
    
    memberInputs.forEach(memberDiv => {
        const nameInput = memberDiv.querySelector('input[type="text"]');
        const emailInput = memberDiv.querySelector('input[type="email"]');
        const roleSelect = memberDiv.querySelector('select');
        
        if (nameInput.value.trim() && emailInput.value.trim()) {
            members.push({
                name: nameInput.value.trim(),
                email: emailInput.value.trim(),
                role: roleSelect.value
            });
        }
    });
    
    if (members.length === 0) {
        alert('Please add at least one group member.');
        return;
    }
    
    // Check if there's at least one leader
    const hasLeader = members.some(member => member.role === 'leader');
    if (!hasLeader) {
        alert('Please assign at least one member as group leader.');
        return;
    }
    
    const newGroup = {
        id: groupsData.length + 1,
        name: document.getElementById('groupName').value.trim(),
        description: document.getElementById('groupDescription').value.trim(),
        adviser_id: null, // Would be set based on current user or selection
        adviser_name: 'To be assigned',
        status: 'active',
        created_at: new Date().toISOString(),
        updated_at: new Date().toISOString(),
        members: members,
        current_document: document.getElementById('thesisTitle').value.trim()
    };
    
    groupsData.push(newGroup);
    updateDashboardStats();
    
    // In a real application, this would be an API call
    // await createGroup(newGroup);
    
    showNotification('Group registered successfully!', 'success');
    hideAddGroupModal();
    
    if (document.getElementById('groups-section') && !document.getElementById('groups-section').classList.contains('hidden')) {
        populateGroupsTable();
    }
}

// Filtering functions
function filterGroups() {
    const statusFilter = document.getElementById('groupStatusFilter').value.toLowerCase();
    const searchFilter = document.getElementById('groupSearchInput').value.toLowerCase();
    
    let filteredGroups = groupsData;
    
    if (statusFilter) {
        filteredGroups = filteredGroups.filter(group => group.status === statusFilter);
    }
    
    if (searchFilter) {
        filteredGroups = filteredGroups.filter(group => 
            group.name.toLowerCase().includes(searchFilter) ||
            group.current_document.toLowerCase().includes(searchFilter) ||
            group.members.some(member => member.name.toLowerCase().includes(searchFilter))
        );
    }
    
    populateGroupsTableWithData(filteredGroups);
}

function filterSubmissions() {
    const statusFilter = document.getElementById('submissionStatusFilter').value.toLowerCase();
    const searchFilter = document.getElementById('submissionSearchInput').value.toLowerCase();
    
    let filteredSubmissions = documentsData;
    
    if (statusFilter) {
        filteredSubmissions = filteredSubmissions.filter(doc => doc.status === statusFilter);
    }
    
    if (searchFilter) {
        filteredSubmissions = filteredSubmissions.filter(doc => 
            doc.title.toLowerCase().includes(searchFilter) ||
            doc.group_name.toLowerCase().includes(searchFilter)
        );
    }
    
    populateSubmissionsTableWithData(filteredSubmissions);
}

function populateGroupsTableWithData(data) {
    const tableBody = document.getElementById('groupsTableBody');
    if (!tableBody) return;
    
    tableBody.innerHTML = '';
    
    data.forEach(group => {
        const row = document.createElement('tr');
        row.className = 'hover:bg-gray-50';
        
        const statusClass = getStatusClass(group.status);
        const statusText = group.status.charAt(0).toUpperCase() + group.status.slice(1).replace('_', ' ');
        
        row.innerHTML = `
            <td class="table-cell font-medium">GRP${String(group.id).padStart(3, '0')}</td>
            <td class="table-cell">${group.name}</td>
            <td class="table-cell">
                <div class="text-sm space-y-1">
                    ${group.members.map(member => 
                        `<div class="flex items-center justify-between">
                            <span class="font-medium">${member.name}</span>
                            <span class="text-xs px-2 py-1 rounded ${member.role === 'leader' ? 'bg-blue-100 text-blue-800' : 'bg-gray-100 text-gray-600'}">${member.role}</span>
                        </div>
                        <div class="text-xs text-gray-500">${member.email}</div>`
                    ).join('')}
                </div>
            </td>
            <td class="table-cell">${group.current_document || 'No active document'}</td>
            <td class="table-cell">
                <span class="px-2 py-1 rounded-full text-xs font-semibold ${statusClass}">
                    ${statusText}
                </span>
            </td>
            <td class="table-cell">${formatDate(group.created_at)}</td>
            <td class="table-cell">
                <div class="flex space-x-2">
                    <button onclick="viewGroup(${group.id})" class="text-blue-600 hover:text-blue-800 text-sm font-medium">View</button>
                    <button onclick="editGroup(${group.id})" class="text-green-600 hover:text-green-800 text-sm font-medium">Edit</button>
                    <button onclick="manageProgress(${group.id})" class="text-purple-600 hover:text-purple-800 text-sm font-medium">Progress</button>
                </div>
            </td>
        `;
        
        tableBody.appendChild(row);
    });
    
    if (data.length === 0) {
        tableBody.innerHTML = '<tr><td colspan="7" class="table-cell text-center text-gray-500 py-8">No groups found matching your criteria.</td></tr>';
    }
}

function populateSubmissionsTableWithData(data) {
    const tableBody = document.getElementById('submissionsTableBody');
    if (!tableBody) return;
    
    tableBody.innerHTML = '';
    
    data.forEach(document => {
        const row = document.createElement('tr');
        row.className = 'hover:bg-gray-50';
        
        const statusClass = getDocumentStatusClass(document.status);
        const statusText = document.status.replace('_', ' ').replace(/\b\w/g, l => l.toUpperCase());
        const fileSize = formatFileSize(document.file_size);
        
        row.innerHTML = `
            <td class="table-cell font-medium">DOC${String(document.id).padStart(3, '0')}</td>
            <td class="table-cell">${document.group_name}</td>
            <td class="table-cell">
                <div class="font-medium">${document.title}</div>
                <div class="text-xs text-gray-500">Type: ${document.type.charAt(0).toUpperCase() + document.type.slice(1)} • Size: ${fileSize}</div>
            </td>
            <td class="table-cell">${formatDate(document.submitted_at)}</td>
            <td class="table-cell">
                <span class="px-2 py-1 rounded-full text-xs font-semibold ${statusClass}">
                    ${statusText}
                </span>
            </td>
            <td class="table-cell">
                <div class="flex space-x-2">
                    <button onclick="reviewDocument(${document.id})" class="text-blue-600 hover:text-blue-800 text-sm font-medium">Review</button>
                    <button onclick="downloadDocument(${document.id})" class="text-green-600 hover:text-green-800 text-sm font-medium">Download</button>
                    <button onclick="viewComments(${document.id})" class="text-purple-600 hover:text-purple-800 text-sm font-medium">Comments</button>
                </div>
            </td>
        `;
        
        tableBody.appendChild(row);
    });
    
    if (data.length === 0) {
        tableBody.innerHTML = '<tr><td colspan="6" class="table-cell text-center text-gray-500 py-8">No submissions found matching your criteria.</td></tr>';
    }
}

// Action functions
function viewGroup(groupId) {
    const group = groupsData.find(g => g.id === groupId);
    if (!group) {
        showNotification('Group not found', 'error');
        return;
    }  
}