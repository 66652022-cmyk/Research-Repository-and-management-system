document.addEventListener('DOMContentLoaded', loadDashboardData);

function loadDashboardData() {
    fetch('../director_api/dashboard-data.php')
        .then(res => res.json())
        .then(data => {
        
            document.getElementById('active-groups').textContent = data.active_groups;
            document.getElementById('active-panels').textContent = data.active_panels;
            document.getElementById('pending-publish').textContent = data.pending;
            document.getElementById('completed-groups').textContent = data.completed_groups;
            document.getElementById('english-critique-count').textContent = data.english_critiques;
            document.getElementById('statistician-count').textContent = data.statisticians;
            document.getElementById('financial-analyst-count').textContent = data.financial_analysts;


            const totalGroups = data.active_groups || 1;
            for (let y = 1; y <= 4; y++) {
                const count = data.year_counts[y] || 0;
                const percent = (count / totalGroups) * 100;
                document.getElementById(`year-${y}-bar`).style.width = percent + '%';
                document.getElementById(`year-${y}-count`).textContent = count;
            }

            const courseList = document.getElementById('course-list');
            courseList.innerHTML = '';
            if (data.course_counts.length === 0) {
                courseList.innerHTML = `<div class="text-gray-500 text-sm">No course data available</div>`;
            } else {
                data.course_counts.forEach(course => {
                    const percent = (course.total / totalGroups) * 100;
                    courseList.innerHTML += `
                        <div class="flex items-center justify-between">
                            <span class="text-sm font-medium text-gray-600">${course.course}</span>
                            <div class="flex items-center space-x-2">
                                <div class="w-24 bg-gray-200 rounded-full h-2">
                                    <div class="bg-royal-blue h-2 rounded-full" style="width: ${percent}%"></div>
                                </div>
                                <span class="text-sm font-semibold text-gray-900">${course.total}</span>
                            </div>
                        </div>
                    `;
                });
            }

            const activity = document.getElementById('recent-activity');
            activity.innerHTML = '';
            data.recent_activity.forEach(act => {
                activity.innerHTML += `
                    <div class="flex items-center space-x-3 p-4 rounded-lg bg-blue-50">
                        <div class="w-2 h-2 bg-blue-500 rounded-full"></div>
                        <div class="flex-1">
                            <p class="text-sm font-medium text-gray-900">
                                ${act.title} (${act.group_name})
                            </p>
                            <p class="text-xs text-gray-500">${timeAgo(act.activity_time)}</p>
                        </div>
                    </div>
                `;
            });
        })
        .catch(err => console.error('Error loading dashboard:', err));
}

function timeAgo(dateString) {
    const date = new Date(dateString);
    const now = new Date();
    const diff = Math.floor((now - date) / 1000);
    if (diff < 60) return `${diff}s ago`;
    if (diff < 3600) return `${Math.floor(diff / 60)}m ago`;
    if (diff < 86400) return `${Math.floor(diff / 3600)}h ago`;
    return `${Math.floor(diff / 86400)}d ago`;
}
