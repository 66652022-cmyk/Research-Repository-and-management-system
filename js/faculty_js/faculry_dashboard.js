document.addEventListener('DOMContentLoaded', () => {
    const menuButton = document.getElementById('menu-button');
    const sidebar = document.getElementById('sidebar');
    const sidebarOverlay = document.getElementById('sidebar-overlay');
    const closeSidebar = document.getElementById('close-sidebar');

    function openSidebar() {
        sidebar.classList.remove('translate-x-full');
        sidebarOverlay.classList.remove('hidden');
    }

    function closeSidebarFn() {
        sidebar.classList.add('translate-x-full');
        sidebarOverlay.classList.add('hidden');
    }

    menuButton.addEventListener('click', openSidebar);
    closeSidebar.addEventListener('click', closeSidebarFn);
    sidebarOverlay.addEventListener('click', closeSidebarFn);

    window.showSection = function(sectionName) {
        // taga tago ng ibang sections
        document.querySelectorAll('.section').forEach(section => {
            section.classList.add('hidden');
        });

        // taga pakita ng current section
        const targetSection = document.getElementById(sectionName + '-section');
        if (targetSection) {
            targetSection.classList.remove('hidden');
        }

        // Update active nav item
        document.querySelectorAll('.nav-item').forEach(item => {
            item.classList.remove('active');
        });

        // taga hanap ngsection na pinindot like, showSection(sectionName)
        const navItems = document.querySelectorAll('.nav-item');
        navItems.forEach(item => {
            if (item.getAttribute('onclick') === `showSection('${sectionName}')`) {
                item.classList.add('active');
            }
        });

        closeSidebarFn();
    };

    window.confirmLogout = function() {
        if (confirm("Are you sure you want to log out?")) {
            window.location.href = '/THESIS/classes/LogoutHandling.php';
        }
    };

    showSection('dashboard');
});

function showAddStudentModal() {
    document.getElementById('addStudentModal').classList.remove('hidden');
    document.getElementById('addStudentModal').classList.add('flex');
}

function hideAddStudentModal() {
    document.getElementById('addStudentModal').classList.add('hidden');
    document.getElementById('addStudentModal').classList.remove('flex');
}

function submitNewStudent(event) {
    event.preventDefault();
    const form = event.target;
    const formData = new FormData(form);

    fetch('/THESIS/faculty_api/create_student.php', {
        method: 'POST',
        body: formData
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            alert('Student added successfully!');
            hideAddStudentModal();
            loadStudents(); // nagre refresh pag may bagong student
        } else {
            alert('Error: ' + data.message);
        }
    })
    .catch(err => {
        alert('Failed to add student: ' + err.message);
    });
}

function loadStudents() {
    fetch('/THESIS/faculty_api/get_students.php')
        .then(res => res.json())
        .then(data => {
            const tbody = document.getElementById('studentsTableBody');
            tbody.innerHTML = '';
            data.forEach(student => {
                const tr = document.createElement('tr');
                tr.classList.add('hover:bg-gray-50');
                tr.innerHTML = `
                    <td class="px-6 py-4 whitespace-nowrap">${student.id}</td>
                    <td class="px-6 py-4 whitespace-nowrap">${student.name}</td>
                    <td class="px-6 py-4 whitespace-nowrap">${student.email}</td>
                    <td class="px-6 py-4 whitespace-nowrap">${student.course}</td>
                    <td class="px-6 py-4 whitespace-nowrap">${student.status}</td>
                    <td class="px-6 py-4 whitespace-nowrap space-x-2 text-sm font-medium">
                        <button class="text-royal-blue hover:text-royal-blue-dark" onclick="editStudent(${student.id})">Edit</button>
                        <button class="text-red-600 hover:text-red-900" onclick="deleteStudent(${student.id})">Delete</button>
                    </td>
                `;
                tbody.appendChild(tr);
            });
        })
        .catch(err => {
            alert('Failed to load students: ' + err.message);
        });
}

// Call loadStudents() pag naka open nayung students section
window.showSection = function(sectionName) {
    document.querySelectorAll('.section').forEach(section => {
        section.classList.add('hidden');
    });

    const targetSection = document.getElementById(sectionName + '-section');
    if (targetSection) {
        targetSection.classList.remove('hidden');
    }

    // Update active nav item
    document.querySelectorAll('.nav-item').forEach(item => {
        item.classList.remove('active');
    });
    const navItems = document.querySelectorAll('.nav-item');
    navItems.forEach(item => {
        if (item.getAttribute('onclick') === `showSection('${sectionName}')`) {
            item.classList.add('active');
        }
    });

    if (sectionName === 'students') {
        loadStudents();
    } else if (sectionName === 'groups') {
        loadGroups(); // Implement similarly to load groups
    }

    closeSidebarFn();
};

function showAddGroupModal() {
    // Load students dynamically
    fetch('/THESIS/faculty_api/get_students.php')
        .then(res => {
            if (!res.ok) throw new Error('Network response was not ok');
            return res.json();
        })
        .then(data => {
            const select = document.getElementById('groupMembers');
            select.innerHTML = ''; // Clear existing options
            data.forEach(student => {
                const option = document.createElement('option');
                option.value = student.id;
                option.textContent = student.name;
                select.appendChild(option);
            });
        })
        .catch(err => {
            alert('Failed to load students: ' + err.message);
        });

    // Load users for each role
    loadUsersByRole('adviser', 'adviser_id');
    loadUsersByRole('english_critique', 'english_critique_id');
    loadUsersByRole('statistician', 'statistician_id');
    loadUsersByRole('financial_analyst', 'financial_analyst_id');

    // Show modal
    const modal = document.getElementById('addGroupModal');
    modal.classList.remove('hidden');
    modal.classList.add('flex');
}

function loadUsersByRole(role, selectId) {
    fetch(`/THESIS/faculty_api/get_users_by_role.php?role=${role}`)
        .then(res => {
            if (!res.ok) throw new Error('Network response was not ok');
            return res.json();
        })
        .then(data => {
            const select = document.getElementById(selectId);
            select.innerHTML = '<option value="">Select ' + role.replace('_', ' ').replace(/\b\w/g, l => l.toUpperCase()) + '</option>';
            data.forEach(user => {
                const option = document.createElement('option');
                option.value = user.id;
                option.textContent = user.name;
                select.appendChild(option);
            });
        })
        .catch(err => {
            console.error('Failed to load ' + role + ': ' + err.message);
        });
}

function hideAddGroupModal() {
    const modal = document.getElementById('addGroupModal');
    modal.classList.add('hidden');
    modal.classList.remove('flex');
    // Reset form
    document.getElementById('addGroupForm').reset();
}

function submitNewGroup(event) {
    event.preventDefault();
    const form = event.target;
    const formData = new FormData(form);

    // Validate at least one member selected
    if (!formData.getAll('groupMembers[]').length) {
        alert('Please select at least one member.');
        return;
    }

    fetch('/THESIS/faculty_api/create_group.php', {
        method: 'POST',
        body: formData
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            console.log('Group created successfully!');
            hideAddGroupModal();
            // Optionally reload groups table here
            if (typeof loadGroups === 'function') loadGroups();
        } else {
            console.error('Error: ' + (data.message || 'Failed to create group'));
        }
    })
    .catch(err => {
        console.error('Failed to create group: ' + err.message);
    });
}

// edit delete below dito
