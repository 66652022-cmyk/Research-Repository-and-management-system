
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <link rel="stylesheet" href="../src/style.css">
    <title>Group Details</title>
    <style>
    .btn-blue {
    background-color: #1e40af;
    color: #fff;
    border: none;
    padding: 6px 12px;
    margin: 2px;
    border-radius: 5px;
    cursor: pointer;
    font-weight: 500;
    transition: background-color 0.2s;
}

.btn-blue:hover {
    background-color: #2563eb;
}

.comment-textarea {
    width: 100%;
    min-height: 36px;
    padding: 6px;
    margin-top: 4px;
    border-radius: 5px;
    border: 1px solid #cbd5e1;
    resize: none;
}
</style>
</head>
<body>
    <!-- Header -->
    <header class="header bg-royal-blue">
        <div class="header-content">
            <h1 id="group-title">Loading...</h1>
            <div class="user-info">
                <div class="user-avatar" style="width: 40px; height: 40px;">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="#FFFFFF" width="70%" height="70%">
                        <path d="M12 12c2.7 0 4.8-2.1 4.8-4.8S14.7 2.4 12 2.4 7.2 4.5 7.2 7.2 9.3 12 12 12zm0 2.4c-3.2 0-9.6 1.6-9.6 4.8v2.4h19.2v-2.4c0-3.2-6.4-4.8-9.6-4.8z"/>
                    </svg>
                </div>
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
        //para sa header
        async function loadGroupHeader() {
            try {
                const res = await fetch('../queries/get_my_group.php');
                const data = await res.json();

                if (data.success) {
                    // Dynamic title
                    document.getElementById('group-title').textContent =
                        `${data.group.research_topic} - Details`;
                } else {
                    document.getElementById('group-title').textContent = 'No group assigned';
                }
            } catch (err) {
                console.error('Error loading group title:', err);
            }
        }

        loadGroupHeader();

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
            
            fetch(`../queries/get_document.php?id=${docId}`)
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
    // Load group members
    async function loadGroupData() {
        const res = await fetch(`../queries/get_group_data.php`);
        const data = await res.json();

        const sidebar = document.querySelector('.groups-list');
        const container = document.getElementById('documents-container');

        sidebar.innerHTML = '';
        container.innerHTML = '<p class="text-gray-500 italic">Select a member to view submissions.</p>';

        if (data.success && data.members.length > 0) {
            data.members.forEach((member, index) => {
                // Sidebar item
                const memberDiv = document.createElement('div');
                memberDiv.className = `
                    flex items-center gap-3 p-2 rounded-lg cursor-pointer
                    hover:bg-blue-100 transition-colors duration-200
                `;
                memberDiv.innerHTML = `
                    <div class="author-avatar bg-royal-blue text-white font-bold rounded-full 
                                w-9 h-9 flex items-center justify-center">
                        ${member.name.charAt(0).toUpperCase()}
                    </div>
                    <span class="font-medium">${member.name}</span>
                `;

                memberDiv.addEventListener('click', () => {
                    document.querySelectorAll('.groups-list div').forEach(el => {
                        el.classList.remove('bg-blue-200');
                    });
                    memberDiv.classList.add('bg-blue-200');
                    renderMemberSubmissions(member);
                });

                sidebar.appendChild(memberDiv);

                if (index === 0) {
                    memberDiv.classList.add('bg-blue-200');
                    renderMemberSubmissions(member);
                }
            });
        } else {
            sidebar.innerHTML = '<p class="text-gray-500 italic">No members found in this group.</p>';
        }
    }

    // Render submissions for one member (new design)
    function renderMemberSubmissions(member) {
        const container = document.getElementById('documents-container');
        container.innerHTML = `
            <h3 class="font-semibold text-xl mb-4 text-gray-800 border-b pb-2">
                ${member.name}'s Submissions
            </h3>
        `;

        if (member.submissions.length > 0) {
            member.submissions.forEach(doc => {
                const div = document.createElement('div');
                div.className = 'document-post mb-4 p-4 border rounded-lg bg-white shadow-md';

                div.innerHTML = `
                    <div class="post-header flex justify-between items-start">
                        <div class="post-author flex items-center gap-3">
                            <div class="author-avatar bg-royal-blue text-white font-bold rounded-full 
                                        w-9 h-9 flex items-center justify-center">
                                ${member.name.charAt(0).toUpperCase()}
                            </div>
                            <div class="author-info">
                                <h4 class="font-semibold">${member.name}</h4>
                                <div class="post-time text-sm text-gray-500">
                                    ${doc.submitted_at ? new Date(doc.submitted_at).toLocaleString() : "N/A"}
                                </div>
                            </div>
                        </div>
                        <div class="document-info text-right">
                            <h5 class="font-medium">${doc.title} <span class="text-sm text-gray-500">(${doc.type})</span></h5>
                            <div class="doc-meta text-sm text-gray-600">
                                ${doc.mime_type || "Unknown"} • ${(doc.file_size ? (doc.file_size/1024).toFixed(1) : 0)} KB
                            </div>
                        </div>
                    </div>

                    <div class="post-actions mt-3 flex gap-2">
                        <button ${doc.file_path ? `onclick="window.open('${doc.file_path}', '_blank')"` : "disabled"} 
                            class="action-btn btn-blue">Download</button>
                        <button onclick="viewDocument(${doc.id})" class="action-btn btn-blue">View</button>
                        <button class="action-btn btn-blue comment-toggle">Comment</button>
                    </div>

                    <div class="comments-preview mt-2 text-sm text-gray-500">
                        <div class="comment-preview">Loading comments...</div>
                    </div>

                    <div class="comments-section mt-2 hidden">
                        <div class="existing-comments text-sm mb-2">Loading comments...</div>
                        <div class="comment-input-container relative flex items-end">
                            <textarea class="comment-textarea flex-1 resize-none border rounded-md p-2 text-sm" 
                                    placeholder="Write a comment..." style="min-height:36px; padding-right:40px;"></textarea>
                            <button class="comment-submit btn-blue absolute right-2 bottom-2 p-2 rounded-full flex items-center justify-center">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="white" viewBox="0 0 24 24">
                                <path d="M2.01 21L23 12 2.01 3 2 10l15 2-15 2z"/>
                                </svg>
                            </button>
                        </div>
                    </div>
                `;

                container.appendChild(div);

                // --- Comment system setup ---
                const commentsSection = div.querySelector('.comments-section');
                const previewDiv = div.querySelector('.comments-preview');
                const existingCommentsDiv = div.querySelector('.existing-comments');
                const textarea = div.querySelector('.comment-textarea');
                let currentReplyTo = null;

                // Toggle comment section
                div.querySelector('.comment-toggle').addEventListener('click', () => {
                    if (commentsSection.classList.contains('hidden')) {
                        commentsSection.classList.remove('hidden');
                        previewDiv.classList.add('hidden');
                    } else {
                        commentsSection.classList.add('hidden');
                        previewDiv.classList.remove('hidden');
                        currentReplyTo = null;
                        textarea.placeholder = 'Write a comment...';
                    }
                });

                // Auto-resize textarea
                textarea.addEventListener('input', function() {
                    this.style.height = 'auto';
                    this.style.height = Math.max(36, this.scrollHeight) + 'px';
                });

                // Fetch comments
                fetch(`../queries/get_comments.php?document_id=${doc.id}`)
                    .then(r => r.json())
                    .then(dataComments => {
                        existingCommentsDiv.innerHTML = '';
                        if (dataComments.success && dataComments.comments.length > 0) {
                            dataComments.comments.forEach(c => renderComment(c, existingCommentsDiv, doc.id));
                            const first = dataComments.comments[0];
                            previewDiv.innerHTML = `<div class="comment-preview"><strong>${first.user_name}</strong>: ${first.comment}</div>`;
                        } else {
                            existingCommentsDiv.innerHTML = '<div>No comments yet</div>';
                            previewDiv.innerHTML = '<div class="comment-preview">No comments</div>';
                        }
                    });

                // Submit comment
                div.querySelector('.comment-submit').addEventListener('click', async () => {
                    const text = textarea.value.trim();
                    if (!text) return;

                    const resPost = await fetch('../queries/post_comments.php', {
                        method: 'POST',
                        headers: {'Content-Type': 'application/json'},
                        body: JSON.stringify({document_id: doc.id, comment: text, parent_id: currentReplyTo})
                    });
                    const result = await resPost.json();
                    if (result.success) {
                        renderComment({id: result.comment_id, user_name: 'You', comment: text, parent_id: currentReplyTo}, existingCommentsDiv, doc.id, currentReplyTo ? 16 : 0);
                        textarea.value = '';
                        textarea.style.height = '36px';
                        currentReplyTo = null;
                        textarea.placeholder = 'Write a comment...';
                    } else {
                        alert('Failed to post comment');
                    }
                });

                // Helper
                function renderComment(c, container, docId, indent = 0) {
                    const commentDiv = document.createElement('div');
                    commentDiv.className = 'comment-item text-sm my-1';
                    commentDiv.style.marginLeft = `${indent}px`;
                    commentDiv.innerHTML = `
                        <strong>${c.user_name}</strong>: ${c.comment} 
                        <button class="reply-btn text-blue-600 text-xs ml-2">Reply</button>
                    `;
                    container.appendChild(commentDiv);

                    commentDiv.querySelector('.reply-btn').addEventListener('click', () => {
                        currentReplyTo = c.id;
                        textarea.focus();
                        textarea.placeholder = `Replying to ${c.user_name}...`;
                    });
                }
            });
        } else {
            container.innerHTML += `<p class="text-gray-500 italic">No submissions from this member.</p>`;
        }
    }

    loadGroupData();

    </script>
</body>
</html>