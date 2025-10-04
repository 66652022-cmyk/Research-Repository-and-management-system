document.addEventListener("DOMContentLoaded", () => {
    const sidebar = document.getElementById("sidebar");
    const overlay = document.getElementById("sidebar-overlay");
    const menuBtn = document.getElementById("menu-button");
    const closeBtn = document.getElementById("close-sidebar");

    function openSidebar() {
        sidebar.classList.remove("-translate-x-full");
        overlay.classList.remove("hidden");
    }

    function closeSidebar() {
        sidebar.classList.add("-translate-x-full");
        overlay.classList.add("hidden");
    }

    menuBtn.addEventListener("click", openSidebar);
    closeBtn.addEventListener("click", closeSidebar);
    overlay.addEventListener("click", closeSidebar);
});

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

    const navItems = document.querySelectorAll('.nav-item');
    navItems.forEach(item => {
        if (item.getAttribute('onclick') === `showSection('${sectionName}')`) {
            item.classList.add('active');
        }
    });

 
};

// -------------------- STUDENT MODAL --------------------
    function showAddStudentModal() {
        const modal = document.getElementById('addStudentModal');
        modal.classList.remove('hidden');
        modal.classList.add('flex');
    }

    function hideAddStudentModal() {
        const modal = document.getElementById('addStudentModal');
        modal.classList.add('hidden');
        modal.classList.remove('flex');
    }

    function submitNewStudent(event) {
        event.preventDefault();
        const form = event.target;
        const formData = new FormData(form);

        fetch('../../faculty_api/create_student.php', {
            method: 'POST',
            body: formData
        })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    alert('✅ Student added successfully!');
                    hideAddStudentModal();
                    loadStudents(); // refresh students list
                } else {
                    alert('❌ Error: ' + data.message);
                }
            })
            .catch(err => {
                alert('❌ Failed to add student: ' + err.message);
            });
    }

    function loadStudents() {
        fetch('../faculty_api/get_students.php')
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
                alert('❌ Failed to load students: ' + err.message);
            });
    }

    // -------------------- GROUP MODAL --------------------
    let groupMembersChoices = null;

    function showAddGroupModal() {
        // Fetch students list
        fetch('../faculty_api/get_students.php')
            .then(res => {
                if (!res.ok) throw new Error('Network response was not ok');
                return res.json();
            })
            .then(data => {
                const select = document.getElementById('groupMembers');

                select.innerHTML = '';

                data.forEach(student => {
                    const option = document.createElement('option');
                    option.value = student.id;
                    option.textContent = student.name;
                    select.appendChild(option);
                });

                if (groupMembersChoices) {
                    groupMembersChoices.destroy();
                }
                groupMembersChoices = new Choices(select, {
                    removeItemButton: true,
                    searchEnabled: true,
                    placeholder: true,
                    placeholderValue: 'Select students...',
                    searchPlaceholderValue: 'Type to search student...',
                    itemSelectText: '',
                    shouldSort: false
                });
            })
            .catch(err => {
                alert('❌ Failed to load students: ' + err.message);
            });

        // Show modal
        const modal = document.getElementById('addGroupModal');
        modal.classList.remove('hidden');
        modal.classList.add('flex');
    }

    function hideAddGroupModal() {
        const modal = document.getElementById('addGroupModal');
        modal.classList.add('hidden');
        modal.classList.remove('flex');

        document.getElementById('addGroupForm').reset();

        if (groupMembersChoices) {
            groupMembersChoices.destroy();
            groupMembersChoices = null;
        }
    }

    // Handle form submit
    document.getElementById('addGroupForm').addEventListener('submit', function (e) {
        e.preventDefault();

        const formData = new FormData(this);

        fetch('../faculty_api/create_ group.php', {
            method: 'POST',
            body: formData
        })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    alert('Group added successfully');
                    hideAddGroupModal();
                    // reload table or refresh groups list
                    location.reload();
                } else {
                    alert('❌ ' + data.message);
                }
            })
            .catch(err => {
                alert('❌ Error: ' + err.message);
            });
    });
    
        function confirmLogout() {
            if (confirm("Are you sure you want to log out?")) {
                window.location.href = '../classes/LogoutHandling.php';
            }
        }