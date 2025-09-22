
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <link rel="stylesheet" href="../src/style.css">
    <title>Group Details - Alpha Research Team</title>

</head>
<body>
    <!-- Header -->
    <header class="header bg-royal-blue">
        <div class="header-content">
            <h1>Alpha Research Team - Details</h1>
            <div class="user-info">
                <div class="user-avatar">U</div>
                <span>Welcome back, User!</span>
            </div>
        </div>
    </header>

    <div class="main-layout">
        <!-- Left Sidebar: Groups List -->
        <aside class="left-sidebar">
            <div class="sidebar-header">
                <h2>Research Groups</h2>
            </div>
            <nav class="groups-list">
                <!-- Dynamic groups -->
            </nav>
        </aside>

        <!-- Main Content -->
        <main class="main-content">
            <div class="content-header">
                <h2>Submitted Documents</h2>
                <p class="content-subtitle">
                    Review and comment on thesis documents submitted by group members
                </p>
            </div>

            <!-- Dynamic submissions-->
            <div id="documents-container" class="space-y-4"></div>
        </main>

        <!-- Right Sidebar -->
        <div class="right-sidebar">
            <!-- Calendar Widget -->
            <div class="sidebar-widget">
                <div class="widget-header">
                    <h3>Calendar</h3>
                </div>
                <div class="widget-content">
                    <div class="calendar">
                        <div class="calendar-header">
                            <button class="calendar-nav" id="prevMonth">‹</button>
                            <div id="monthYear" style="font-weight: 600;"></div>
                            <button class="calendar-nav" id="nextMonth">›</button>
                        </div>
                        <div class="calendar-grid">
                            <div class="calendar-day">Sun</div>
                            <div class="calendar-day">Mon</div>
                            <div class="calendar-day">Tue</div>
                            <div class="calendar-day">Wed</div>
                            <div class="calendar-day">Thu</div>
                            <div class="calendar-day">Fri</div>
                            <div class="calendar-day">Sat</div>
                        </div>
                        <div class="calendar-grid" id="calendarDates"></div>
                    </div>
                </div>
            </div>

            <!-- Thesis Progress Widget -->
            <div class="sidebar-widget">
                <div class="widget-header">
                    <h3>Thesis Progress</h3>
                </div>
                <div class="widget-content">
                    <div class="progress-item">
                        <div class="progress-header">
                            <span class="progress-label">Introduction</span>
                            <span class="progress-percentage">100%</span>
                        </div>
                        <div class="progress-bar">
                            <div class="progress-fill" style="width: 100%;"></div>
                        </div>
                    </div>
                    <div class="progress-item">
                        <div class="progress-header">
                            <span class="progress-label">Literature Review</span>
                            <span class="progress-percentage">80%</span>
                        </div>
                        <div class="progress-bar">
                            <div class="progress-fill" style="width: 80%;"></div>
                        </div>
                    </div>
                    <div class="progress-item">
                        <div class="progress-header">
                            <span class="progress-label">Methodology</span>
                            <span class="progress-percentage">60%</span>
                        </div>
                        <div class="progress-bar">
                            <div class="progress-fill" style="width: 60%;"></div>
                        </div>
                    </div>
                    <div class="progress-item">
                        <div class="progress-header">
                            <span class="progress-label">Results</span>
                            <span class="progress-percentage">40%</span>
                        </div>
                        <div class="progress-bar">
                            <div class="progress-fill" style="width: 40%;"></div>
                        </div>
                    </div>
                    <div class="progress-item">
                        <div class="progress-header">
                            <span class="progress-label">Discussion</span>
                            <span class="progress-percentage">20%</span>
                        </div>
                        <div class="progress-bar">
                            <div class="progress-fill" style="width: 20%;"></div>
                        </div>
                    </div>
                    <div class="progress-item">
                        <div class="progress-header">
                            <span class="progress-label">Conclusion</span>
                            <span class="progress-percentage">10%</span>
                        </div>
                        <div class="progress-bar">
                            <div class="progress-fill" style="width: 10%;"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Auto-resize textareas
        document.querySelectorAll('.comment-textarea').forEach(textarea => {
            textarea.addEventListener('input', function() {
                this.style.height = 'auto';
                this.style.height = Math.max(36, this.scrollHeight) + 'px';
            });

            textarea.addEventListener('keydown', function(e) {
                if (e.key === 'Enter' && !e.shiftKey) {
                    e.preventDefault();
                    // Handle comment submission here
                    console.log('Comment submitted:', this.value);
                    this.value = '';
                    this.style.height = '36px';
                }
            });
        });

        // Mini calendar functionality
        const monthYear = document.getElementById('monthYear');
        const calendarDates = document.getElementById('calendarDates');
        const prevMonthBtn = document.getElementById('prevMonth');
        const nextMonthBtn = document.getElementById('nextMonth');

        let currentDate = new Date();

        function renderCalendar(date) {
            calendarDates.innerHTML = '';
            const year = date.getFullYear();
            const month = date.getMonth();

            monthYear.textContent = date.toLocaleString('default', { month: 'long', year: 'numeric' });

            const firstDay = new Date(year, month, 1);
            const lastDay = new Date(year, month + 1, 0);
            const startDay = firstDay.getDay();
            const totalDays = lastDay.getDate();

            // Fill empty slots before first day
            for (let i = 0; i < startDay; i++) {
                const emptyCell = document.createElement('div');
                calendarDates.appendChild(emptyCell);
            }

            // Fill days
            for (let day = 1; day <= totalDays; day++) {
                const dayCell = document.createElement('div');
                dayCell.textContent = day;
                dayCell.classList.add('calendar-date');

                // Highlight today
                const today = new Date();
                if (day === today.getDate() && month === today.getMonth() && year === today.getFullYear()) {
                    dayCell.classList.add('today');
                }

                calendarDates.appendChild(dayCell);
            }
        }

        prevMonthBtn.addEventListener('click', () => {
            currentDate.setMonth(currentDate.getMonth() - 1);
            renderCalendar(currentDate);
        });

        nextMonthBtn.addEventListener('click', () => {
            currentDate.setMonth(currentDate.getMonth() + 1);
            renderCalendar(currentDate);
        });

        renderCalendar(currentDate);

        // Comment submission functionality
        document.querySelectorAll('.comment-submit').forEach(button => {
            button.addEventListener('click', function() {
                const textarea = this.parentElement.querySelector('.comment-textarea');
                if (textarea.value.trim()) {
                    // Handle comment submission here
                    console.log('Comment submitted:', textarea.value);
                    textarea.value = '';
                    textarea.style.height = '36px';
                }
            });
        });

        document.querySelectorAll('.view-btn').forEach(button => {
        button.addEventListener('click', function() {
            const docId = this.getAttribute('data-document-id');
            
            fetch(`queries/get_document.php?id=${docId}`)
                .then(res => res.json())
                .then(data => {
                    if (data.success) {
                        // I-send sa editor section
                        const editorFrame = document.getElementById('editorFrame');
                        editorFrame.contentWindow.postMessage({
                            type: 'loadDocument',
                            document: data.document
                        }, '*');
                    } else {
                        alert('Failed to load document');
                    }
                });
        });
    });
    // pang render ng groups sa left sidebar
    async function loadGroups() {
        const res = await fetch('../queries/get_groups.php');
        const data = await res.json();

        if (data.success) {
            const sidebar = document.querySelector('.groups-list');
            sidebar.innerHTML = ''; // linisin muna

            data.groups.forEach(group => {
                const el = document.createElement('a');
                el.href = "#";
                el.className = "group-item";
                el.dataset.groupId = group.id;
                el.innerHTML = `
                    <div class="group-icon">${group.name.substring(0,2).toUpperCase()}</div>
                    <div>
                        <div>${group.name}</div>
                        <div style="font-size: 12px; color: #65676b;">${group.members} members</div>
                    </div>
                `;
                el.addEventListener('click', () => {
                    document.querySelectorAll('.group-item').forEach(g => g.classList.remove('active'));
                    el.classList.add('active');
                    loadSubmissions(group.id);
                });
                sidebar.appendChild(el);
            });

            // auto load first group
            if (data.groups.length > 0) {
                sidebar.querySelector('.group-item').classList.add('active');
                loadSubmissions(data.groups[0].id);
            }
        }
    }

    // pang render ng main content (submissions per group)
    async function loadSubmissions(groupId) {
        const res = await fetch('../queries/get_submissions.php?group_id=' + groupId);
        const data = await res.json();

        const container = document.getElementById('documents-container');
        container.innerHTML = '';

        if (data.success && data.documents.length > 0) {
            data.documents.forEach(doc => {
                const div = document.createElement('div');
                div.className = 'document-post';
                div.innerHTML = `
                    <div class="post-header">
                        <div class="post-author">
                            <div class="author-avatar">${doc.uploader_name ? doc.uploader_name.charAt(0).toUpperCase() : "?"}</div>
                            <div class="author-info">
                                <h4>${doc.uploader_name || "Unknown"}</h4>
                                <div class="post-time">${doc.submitted_at ? new Date(doc.submitted_at).toLocaleString() : "N/A"}</div>
                            </div>
                        </div>
                        <div class="document-info">
                            <h5>${doc.title} (${doc.type})</h5>
                            <div class="doc-meta">${doc.mime_type || "Unknown"} • ${(doc.file_size ? (doc.file_size/1024).toFixed(1) : 0)} KB</div>
                        </div>
                    </div>
                    <div class="post-actions">
                        <button ${doc.file_path ? `onclick="window.open('${doc.file_path}', '_blank')"` : "disabled"} class="action-btn">Download</button>
                        <button onclick="viewDocument(${doc.id})" class="action-btn">View</button>
                    </div>
                `;
                container.appendChild(div);
            });
        } else {
            container.innerHTML = `<p>No submissions found for this group.</p>`;
        }
    }

    function viewDocument(docId) {
        alert("Open inline viewer for document ID: " + docId);
        // dito later ilalagay yung PDF/Text editor inline view
    }

    loadGroups();

    </script>
</body>
</html>