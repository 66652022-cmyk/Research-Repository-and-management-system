
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
            <div class="progress-item">
                <div class="sidebar-widget">
                    <div class="widget-header">
                        <h3>Thesis Progress</h3>
                    </div>
                    <div id="progress-container"  class="widget-content">
                        <!-- Progress bars will be injected here by JS -->
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
            const res = await fetch('../queries/get_group_submissions.php');
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
        const res = await fetch(`../queries/get_submissions.php?group_id=${groupId}`);
        const data = await res.json();

        const container = document.getElementById('documents-container');
        container.innerHTML = '';

        if (data.success && data.documents.length > 0) {
            for (const doc of data.documents) {
                const div = document.createElement('div');
                div.className = 'document-post';

                div.innerHTML = `
                <div class="post-header">
                    <div class="post-author">
                        <div class="author-avatar">${doc.submitted_by ? doc.submitted_by.charAt(0).toUpperCase() : "?"}</div>
                        <div class="author-info">
                            <h4>${doc.submitted_by || "Unknown"}</h4>
                            <div class="post-time">${doc.submitted_at ? new Date(doc.submitted_at).toLocaleString() : "N/A"}</div>
                        </div>
                    </div>
                    <div class="document-info">
                        <h5>${doc.title} (${doc.type})</h5>
                        <div class="doc-meta">${doc.mime_type || "Unknown"} • ${(doc.file_size ? (doc.file_size/1024).toFixed(1) : 0)} KB</div>
                    </div>
                </div>

                <div class="post-actions">
                    <button ${doc.file_path ? `onclick="window.open('${doc.file_path}', '_blank')"` : "disabled"} class="action-btn btn-blue">Download</button>
                    <button onclick="viewDocument(${doc.id})" class="action-btn btn-blue">View</button>
                    <button class="action-btn btn-blue comment-toggle">Comment</button>
                </div>

                <div class="comments-preview"><div class="comment-preview">Loading comments...</div></div>

                <div class="comments-section" style="display:none; margin-top: 8px;">
                    <div class="existing-comments">Loading comments...</div>
                    <div class="comment-input-container" style="position: relative; display: flex; align-items: flex-end;">
                        <textarea class="comment-textarea" placeholder="Write a comment..." style="flex:1; padding-right: 40px;"></textarea>
                        <button class="comment-submit btn-blue" style="position: absolute; right: 4px; bottom: 4px; padding:4px 8px; border-radius:50%; display:flex; align-items:center; justify-content:center;">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="white" viewBox="0 0 24 24">
                                <path d="M2 21l21-9L2 3v7l15 2-15 2v7z"/>
                            </svg>
                        </button>
                    </div>
                </div>
            `;
                container.appendChild(div);

                const commentsSection = div.querySelector('.comments-section');
                const previewDiv = div.querySelector('.comments-preview');
                const existingCommentsDiv = div.querySelector('.existing-comments');
                const textarea = div.querySelector('.comment-textarea');
                let currentReplyTo = null;

                // Toggle comment section
                div.querySelector('.comment-toggle').addEventListener('click', () => {
                    if (commentsSection.style.display === 'none') {
                        commentsSection.style.display = 'block';
                        previewDiv.style.display = 'none';
                    } else {
                        commentsSection.style.display = 'none';
                        previewDiv.style.display = 'block';
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
                const resComments = await fetch(`../queries/get_comments.php?document_id=${doc.id}`);
                const dataComments = await resComments.json();
                existingCommentsDiv.innerHTML = '';

                if (dataComments.success && dataComments.comments.length > 0) {
                    dataComments.comments.forEach(c => renderComment(c, existingCommentsDiv, doc.id));
                    const firstComment = dataComments.comments[0];
                    previewDiv.innerHTML = `<div class="comment-preview"><strong>${firstComment.user_name}</strong>: ${firstComment.comment}</div>`;
                } else {
                    existingCommentsDiv.innerHTML = '<div>No comments yet</div>';
                    previewDiv.innerHTML = '<div class="comment-preview">No comments</div>';
                }

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

                // Render comment helper
                function renderComment(c, container, docId, indent = 0) {
                    const commentDiv = document.createElement('div');
                    commentDiv.className = 'comment-item';
                    commentDiv.style.marginLeft = `${indent}px`;
                    commentDiv.innerHTML = `
                        <strong>${c.user_name}</strong>: ${c.comment} 
                        <button class="reply-btn btn-blue" style="font-size:0.8em; margin-left:6px;">Reply</button>
                    `;
                    container.appendChild(commentDiv);

                    commentDiv.querySelector('.reply-btn').addEventListener('click', () => {
                        currentReplyTo = c.id;
                        textarea.focus();
                        textarea.placeholder = `Replying to ${c.user_name}...`;
                    });
                }
            }

            // 🔑 After rendering submissions → load progress
            loadProgress(groupId);

        } else {
            container.innerHTML = `<p>No submissions found for this group.</p>`;
            document.getElementById('progress-container').innerHTML = `<p>No progress available.</p>`;
        }
    }

    function viewDocument(docId) {
        alert("Open inline viewer for document ID: " + docId);
    }

    //Progress loader with chapters + parts
    async function loadProgress(groupId) {
        const res = await fetch(`../queries/get_progress.php?group_id=${groupId}`);
        const data = await res.json();

        const container = document.getElementById('progress-container');
        container.innerHTML = '';

        if (data.success) {
            // Overall progress
            const overall = document.createElement('div');
            overall.className = 'progress-item';
            overall.innerHTML = `
                <div class="progress-header">
                    <span class="progress-label">Overall Progress</span>
                    <span class="progress-percentage">${data.overall.percentage}%</span>
                </div>
                <div class="progress-bar">
                    <div class="progress-fill" style="width: ${data.overall.percentage}%"></div>
                </div>
            `;
            container.appendChild(overall);

            // Chapters
            data.chapters.forEach(ch => {
                const div = document.createElement('div');
                div.className = 'progress-item';

                const header = document.createElement('div');
                header.className = 'progress-header';
                header.innerHTML = `<span>Chapter ${ch.chapter}: ${ch.title}</span>
                                    <span>${ch.percentage}%</span>`;
                div.appendChild(header);

                const bar = document.createElement('div');
                bar.className = 'progress-bar';
                bar.innerHTML = `<div class="progress-fill" style="width:${ch.percentage}%"></div>`;
                div.appendChild(bar);

                // Parts container hidden initially
                const partsContainer = document.createElement('div');
                partsContainer.className = 'chapter-parts';
                partsContainer.style.display = 'none';

                ch.parts.forEach(p => {
                    const pDiv = document.createElement('div');
                    pDiv.className = 'part-item';
                    let statusClass = 'part-status-pending';
                    if (p.status === 'approved') statusClass = 'part-status-approved';
                    else if (p.status === 'revision_needed') statusClass = 'part-status-revision';

                    pDiv.innerHTML = `<span>${p.part}</span><span class="${statusClass}">${p.status}</span>`;
                    partsContainer.appendChild(pDiv);
                });

                div.appendChild(partsContainer);

                // Toggle parts on header click
                header.addEventListener('click', () => {
                    partsContainer.style.display = partsContainer.style.display === 'none' ? 'block' : 'none';
                });

                container.appendChild(div);
            });
        } else {
            container.innerHTML = `<p>${data.message}</p>`;
        }
    }

    loadGroups();

    </script>
</body>
</html>