<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Group Role Assignment</title>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/tailwindcss/2.2.19/tailwind.min.css"></script>
    <style>
        .swal2-container {
            z-index: 10000;
        }
        
        .royal-blue {
            color: #4169E1;
        }
        
        .royal-blue-dark {
            color: #1e40af;
        }
        
        .assign-btn:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body class="bg-gray-100">
    <div class="container mx-auto p-6">
        <h1 class="text-2xl font-bold mb-6">Group Management</h1>
        
        <!-- Sample table structure -->
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 bg-white shadow rounded-lg">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Group</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Name</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Members</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Description</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Created</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 whitespace-nowrap">#GRP001</td>
                        <td class="px-6 py-4 whitespace-nowrap font-semibold">Project Alpha</td>
                        <td class="px-6 py-4 whitespace-nowrap">John Doe, Jane Smith, Mike Johnson</td>
                        <td class="px-6 py-4 whitespace-nowrap">Research Project</td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">Active</span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">2024-01-15</td>
                        <td class="px-6 py-4 whitespace-nowrap space-x-2 text-sm font-medium">
                            <button class="assign-btn text-royal-blue hover:text-royal-blue-dark" data-group-id="1" data-group-name="Project Alpha" data-members='["John Doe", "Jane Smith", "Mike Johnson"]'>
                                assign critiques
                            </button>
                        </td>
                    </tr>
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 whitespace-nowrap">#GRP002</td>
                        <td class="px-6 py-4 whitespace-nowrap font-semibold">Project Beta</td>
                        <td class="px-6 py-4 whitespace-nowrap">Sarah Wilson, Tom Brown, Lisa Davis</td>
                        <td class="px-6 py-4 whitespace-nowrap">Analysis Project</td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">Completed</span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">2024-01-10</td>
                        <td class="px-6 py-4 whitespace-nowrap space-x-2 text-sm font-medium">
                            <button class="assign-btn text-royal-blue hover:text-royal-blue-dark" data-group-id="2" data-group-name="Project Beta" data-members='["Sarah Wilson", "Tom Brown", "Lisa Davis"]'>
                                assign critiques
                            </button>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

    <script>
        // Enhanced SweetAlert2 Role Assignment
        document.addEventListener('DOMContentLoaded', function() {
            const assignButtons = document.querySelectorAll('.assign-btn');
            
            assignButtons.forEach(button => {
                button.addEventListener('click', function() {
                    const groupId = this.getAttribute('data-group-id');
                    const groupName = this.getAttribute('data-group-name');
                    const members = JSON.parse(this.getAttribute('data-members'));
                    
                    showRoleAssignmentModal(groupId, groupName, members);
                });
            });
        });

        function showRoleAssignmentModal(groupId, groupName, members) {
            // Create member options for dropdowns
            const memberOptions = members.map(member => 
                `<option value="${member}">${member}</option>`
            ).join('');

            Swal.fire({
                title: `Assign Roles - ${groupName}`,
                html: `
                    <div class="text-left space-y-4">
                        <div class="mb-4">
                            <p class="text-sm text-gray-600 mb-2">Group ID: <strong>#GRP${String(groupId).padStart(3, '0')}</strong></p>
                            <p class="text-sm text-gray-600">Members: <strong>${members.join(', ')}</strong></p>
                        </div>
                        
                        <div class="grid gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">English Critique</label>
                                <select id="englishCritique" class="w-full p-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                    <option value="">Select a member</option>
                                    ${memberOptions}
                                </select>
                            </div>
                            
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Statistician</label>
                                <select id="statistician" class="w-full p-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                    <option value="">Select a member</option>
                                    ${memberOptions}
                                </select>
                            </div>
                            
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Financial Analyst</label>
                                <select id="financialAnalyst" class="w-full p-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                    <option value="">Select a member</option>
                                    ${memberOptions}
                                </select>
                            </div>
                        </div>
                        
                        <div class="mt-4 p-3 bg-yellow-50 border border-yellow-200 rounded-md">
                            <p class="text-xs text-yellow-800">
                                <strong>Note:</strong> Each member can only be assigned to one role. Make sure all roles are filled before saving.
                            </p>
                        </div>
                    </div>
                `,
                width: '600px',
                showCancelButton: true,
                confirmButtonText: 'Save Assignments',
                cancelButtonText: 'Cancel',
                confirmButtonColor: '#3b82f6',
                cancelButtonColor: '#ef4444',
                showLoaderOnConfirm: true,
                preConfirm: () => {
                    const englishCritique = document.getElementById('englishCritique').value;
                    const statistician = document.getElementById('statistician').value;
                    const financialAnalyst = document.getElementById('financialAnalyst').value;
                    
                    // Validation
                    if (!englishCritique || !statistician || !financialAnalyst) {
                        Swal.showValidationMessage('Please assign all three roles');
                        return false;
                    }
                    
                    // Check for duplicate assignments
                    const assignments = [englishCritique, statistician, financialAnalyst];
                    const uniqueAssignments = [...new Set(assignments)];
                    
                    if (assignments.length !== uniqueAssignments.length) {
                        Swal.showValidationMessage('Each member can only be assigned to one role');
                        return false;
                    }
                    
                    return {
                        groupId: groupId,
                        englishCritique: englishCritique,
                        statistician: statistician,
                        financialAnalyst: financialAnalyst
                    };
                },
                allowOutsideClick: () => !Swal.isLoading()
            }).then((result) => {
                if (result.isConfirmed) {
                    // Save to database
                    saveRoleAssignments(result.value);
                }
            });
        }

        function saveRoleAssignments(assignments) {
            // AJAX call to save assignments to database
            fetch('save_role_assignments.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify(assignments)
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    Swal.fire({
                        title: 'Success!',
                        text: 'Role assignments have been saved successfully.',
                        icon: 'success',
                        confirmButtonColor: '#10b981',
                        timer: 3000,
                        showConfirmButton: false
                    });
                    
                    // Optionally refresh the page or update the UI
                    // location.reload();
                } else {
                    Swal.fire({
                        title: 'Error!',
                        text: data.message || 'Failed to save role assignments.',
                        icon: 'error',
                        confirmButtonColor: '#ef4444'
                    });
                }
            })
            .catch(error => {
                console.error('Error:', error);
                Swal.fire({
                    title: 'Error!',
                    text: 'An error occurred while saving the assignments.',
                    icon: 'error',
                    confirmButtonColor: '#ef4444'
                });
            });
        }
    </script>
</body>
</html>