<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <link rel="stylesheet" href="../src/style.css">
    <!-- SweetAlert2 CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">

    <!-- SweetAlert2 JS -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.all.min.js"></script>

    <title>Group Details</title>
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
        <!-- Left Sidebar: Group Members -->
        <aside class="left-sidebar">
            <div class="sidebar-header">
                <h2>Group Members</h2>
            </div>
            <nav class="groups-list">
                <!-- Dynamic members -->
            </nav>
        </aside>

        <!-- Main Content -->
        <main class="main-content">
            <div class="content-header">
                <h2>Submitted Documents</h2>
                <p class="content-subtitle">
                    Review and comment on thesis documents submitted by group members
                </p>
                <button id="uploadDocBtn" style="
                    position: fixed;
                    bottom: 20px;
                    right: 20px;
                    padding: 12px 20px;
                    background-color: #3b82f6;
                    color: white;
                    border: none;
                    border-radius: 8px;
                    cursor: pointer;
                    box-shadow: 0 4px 6px rgba(0,0,0,0.2);
                    z-index: 1000;
                ">
                    Upload Document
                </button>
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
                    <div id="progress-container" class="widget-content">
                        <!-- Progress bars will be injected here by JS -->
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- upload form -->
    <!-- <div class="document-post">
    <div class="post-header">
        <h3>Submit New Document</h3>
    </div>
    <div class="post-description">
        <form id="uploadDocumentForm" enctype="multipart/form-data">
            <div style="margin-bottom:12px;">
                <label for="docTitle">Title:</label><br>
                <input type="text" id="docTitle" name="title" required
                    class="comment-textarea-custom" placeholder="Document Title">
            </div>

            <div style="margin-bottom:12px;">
                <label for="docType">Type:</label><br>
                <select id="docType" name="type" class="comment-textarea-custom" required>
                    <option value="proposal">Proposal</option>
                    <option value="chapter1">Chapter 1</option>
                    <option value="chapter2">Chapter 2</option>
                    <option value="chapter3">Chapter 3</option>
                    <option value="chapter4">Chapter 4</option>
                    <option value="chapter5">Chapter 5</option>
                    <option value="final">Final</option>
                    <option value="other">Other</option>
                </select>
            </div>

            <div style="margin-bottom:12px;">
                <label for="documentFile">Choose File:</label><br>
                <input type="file" id="documentFile" name="document" class="comment-textarea-custom" required>
            </div>

            <button type="submit" class="btn-blue">Submit</button>
        </form>
        <div id="uploadStatus" style="margin-top:10px;"></div>
    </div>
</div> -->

</body>

<script>
        let currentGroupId = null;

        // Load group header
        async function loadGroupHeader() {
            try {
                const res = await fetch('../queries/get_my_group.php');
                const data = await res.json();

                if (data.success) {
                    document.getElementById('group-title').textContent = `${data.group.research_topic} - Details`;
                    currentGroupId = data.group.id;
                    loadProgress(currentGroupId);
                } else {
                    document.getElementById('group-title').textContent = 'No group assigned';
                }
            } catch (err) {
                console.error('Error loading group title:', err);
            }
        }

        // Calendar functionality
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

            for (let i = 0; i < startDay; i++) {
                const emptyCell = document.createElement('div');
                calendarDates.appendChild(emptyCell);
            }

            for (let day = 1; day <= totalDays; day++) {
                const dayCell = document.createElement('div');
                dayCell.textContent = day;
                dayCell.classList.add('calendar-date');

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

        // Organize comments into tree structure
        function organizeComments(comments) {
            const commentMap = new Map();
            const rootComments = [];

            comments.forEach(comment => {
                comment.replies = [];
                commentMap.set(comment.id, comment);
            });

            comments.forEach(comment => {
                if (comment.parent_id) {
                    const parent = commentMap.get(parseInt(comment.parent_id));
                    if (parent) {
                        parent.replies.push(comment);
                    }
                } else {
                    rootComments.push(comment);
                }
            });

            const sortByTime = (a, b) => new Date(a.created_at) - new Date(b.created_at);
            rootComments.sort(sortByTime);
            rootComments.forEach(comment => {
                if (comment.replies.length > 0) {
                    comment.replies.sort(sortByTime);
                }
            });

            return rootComments;
        }

        // Render comment with replies
        function renderCommentWithReplies(comment, container, textarea, currentReplyToObj, allComments) {
            const commentDiv = document.createElement('div');
            commentDiv.id = `comment-${comment.id}`;
            commentDiv.className = 'comment-item';
            
            let replyToText = '';
            if (comment.parent_id) {
                const parentComment = allComments.find(c => c.id === parseInt(comment.parent_id));
                if (parentComment) {
                    replyToText = `<span class="reply-to">Replied to ${parentComment.user_name}</span>`;
                }
            }
            
            commentDiv.innerHTML = `
                <div class="comment-content">
                    ${replyToText}
                    <div class="comment-text">
                        <strong>${comment.user_name}</strong>: ${comment.comment}
                        <button class="reply-btn btn-blue" style="font-size:0.8em; margin-left:6px;">Reply</button>
                    </div>
                </div>
            `;
            
            const replyBtn = commentDiv.querySelector('.reply-btn');
            if (textarea && currentReplyToObj) {
                replyBtn.addEventListener('click', () => {
                    currentReplyToObj.value = comment.id;
                    textarea.focus();
                    textarea.placeholder = `Replying to ${comment.user_name}...`;
                });
            }
            
            container.appendChild(commentDiv);
            
            if (comment.replies && comment.replies.length > 0) {
                const repliesContainer = document.createElement('div');
                repliesContainer.className = 'comment-thread';
                commentDiv.appendChild(repliesContainer);
                
                comment.replies.forEach(reply => {
                    renderCommentWithReplies(reply, repliesContainer, textarea, currentReplyToObj, allComments);
                });
            }
        }

        // Load group members and their submissions
        async function loadGroupData() {
            const res = await fetch('../queries/get_group_data.php');
            const data = await res.json();

            const sidebar = document.querySelector('.groups-list');
            const container = document.getElementById('documents-container');

            sidebar.innerHTML = '';
            container.innerHTML = '<p style="color: #6b7280; font-style: italic;">Select a member to view submissions.</p>';

            if (data.success && data.members.length > 0) {
                data.members.forEach((member, index) => {
                    const memberDiv = document.createElement('div');
                    memberDiv.className = 'group-item';
                    memberDiv.innerHTML = `
                        <div class="group-icon">${member.name.charAt(0).toUpperCase()}</div>
                        <div>
                            <div>${member.name}</div>
                            <div style="font-size: 12px; color: #65676b;">${member.submissions.length} submissions</div>
                        </div>
                    `;

                    memberDiv.addEventListener('click', () => {
                        document.querySelectorAll('.group-item').forEach(el => el.classList.remove('active'));
                        memberDiv.classList.add('active');
                        renderMemberSubmissions(member);
                    });

                    sidebar.appendChild(memberDiv);

                    if (index === 0) {
                        memberDiv.classList.add('active');
                        renderMemberSubmissions(member);
                    }
                });
            } else {
                sidebar.innerHTML = '<p style="padding: 16px; color: #6b7280; font-style: italic;">No members found.</p>';
            }
        }

        // Render member submissions
        async function renderMemberSubmissions(member) {
            const container = document.getElementById('documents-container');
            container.innerHTML = '';

            if (member.submissions.length === 0) {
                container.innerHTML = `<p style="color: #6b7280; font-style: italic;">No submissions from ${member.name}.</p>`;
                return;
            }

            for (const doc of member.submissions) {
                const div = document.createElement('div');
                div.className = 'document-post';

                div.innerHTML = `
                    <div class="post-header">
                        <div class="post-author">
                            <div class="author-avatar">${member.name.charAt(0).toUpperCase()}</div>
                            <div class="author-info">
                                <h4>${member.name}</h4>
                                <div class="post-time">${doc.submitted_at ? new Date(doc.submitted_at).toLocaleString() : "N/A"}</div>
                            </div>
                        </div>
                    </div>

                    <!-- Caption -->
                    <div class="post-caption">${doc.title || "(No caption)"}</div>

                    <!-- Chapter & Part -->
                    <div class="doc-details">
                        <strong>Chapter:</strong> ${doc.chapter || "N/A"} 
                        <span class="dot">•</span> 
                        <strong>Part:</strong> ${doc.part || "N/A"}
                    </div>

                    <!-- File info -->
                    <div class="doc-meta">
                        ${doc.mime_type || "Unknown"} 
                        <span class="dot">•</span> 
                        ${(doc.file_size ? (doc.file_size/1024).toFixed(1) : 0)} KB
                    </div>

                    <!-- Actions -->
                    <div class="post-actions">
                        <button ${doc.file_path ? `onclick="window.open('${doc.file_path}', '_blank')"` : "disabled"} class="action-btn btn-blue">Download</button>
                        <button onclick="viewDocument(${doc.id})" class="action-btn btn-blue">View</button>
                        <button class="action-btn btn-blue comment-toggle">Comment</button>
                    </div>

                    <!-- Comments -->
                    <div class="comments-preview"><div class="comment-preview">Loading comments...</div></div>
                    <div class="comments-section" style="display:none; margin-top: 8px;">
                        <div class="existing-comments">Loading comments...</div>
                        <div class="comment-input-container">
                            <textarea class="comment-textarea" placeholder="Write a comment..."></textarea>
                            <button class="comment-submit btn-blue">
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
                let currentReplyToObj = { value: null };

                // Toggle comments
                div.querySelector('.comment-toggle').addEventListener('click', () => {
                    if (commentsSection.style.display === 'none') {
                        commentsSection.style.display = 'block';
                        previewDiv.style.display = 'none';
                    } else {
                        commentsSection.style.display = 'none';
                        previewDiv.style.display = 'block';
                        currentReplyToObj.value = null;
                        textarea.placeholder = 'Write a comment...';
                    }
                });

                // Auto-resize textarea
                textarea.addEventListener('input', function() {
                    this.style.height = 'auto';
                    this.style.height = Math.max(36, this.scrollHeight) + 'px';
                });

                // Fetch and render comments
                const resComments = await fetch(`../queries/get_comments.php?document_id=${doc.id}`);
                const dataComments = await resComments.json();
                existingCommentsDiv.innerHTML = '';

                if (dataComments.success && dataComments.comments.length > 0) {
                    const rootComments = organizeComments(dataComments.comments);
                    rootComments.forEach(comment => {
                        renderCommentWithReplies(comment, existingCommentsDiv, textarea, currentReplyToObj, dataComments.comments);
                    });
                    
                    const mostRecent = dataComments.comments[dataComments.comments.length - 1];
                    previewDiv.innerHTML = `<div class="comment-preview"><strong>${mostRecent.user_name}</strong>: ${mostRecent.comment}</div>`;
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
                        body: JSON.stringify({
                            document_id: doc.id, 
                            comment: text, 
                            parent_id: currentReplyToObj.value
                        })
                    });
                    const result = await resPost.json();
                    
                    if (result.success) {
                        const resComments = await fetch(`../queries/get_comments.php?document_id=${doc.id}`);
                        const dataComments = await resComments.json();
                        
                        if (dataComments.success) {
                            existingCommentsDiv.innerHTML = '';
                            const rootComments = organizeComments(dataComments.comments);
                            rootComments.forEach(comment => {
                                renderCommentWithReplies(comment, existingCommentsDiv, textarea, currentReplyToObj, dataComments.comments);
                            });

                            const lastComment = dataComments.comments[dataComments.comments.length - 1];
                            previewDiv.innerHTML = `<div class="comment-preview"><strong>${lastComment.user_name}</strong>: ${lastComment.comment}</div>`;
                        }
                        
                        textarea.value = '';
                        textarea.style.height = '36px';
                        currentReplyToObj.value = null;
                        textarea.placeholder = 'Write a comment...';
                    } else {
                        alert('Failed to post comment');
                    }
                });
            }
        }

        // Progress loader with chapters + parts
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
                        <div class="progress-fill" style="width:${data.overall.percentage}%"></div>
                    </div>
                `;
                container.appendChild(overall);

                // Chapters
                data.chapters.forEach(ch => {
                    const div = document.createElement('div');
                    div.className = 'progress-item';

                    const header = document.createElement('div');
                    header.className = 'progress-header';
                    header.style.cursor = 'pointer';
                    header.innerHTML = `<span>Chapter ${ch.chapter}: ${ch.title}</span><span>${ch.percentage}%</span>`;
                    div.appendChild(header);

                    const bar = document.createElement('div');
                    bar.className = 'progress-bar';
                    bar.innerHTML = `<div class="progress-fill" style="width:${ch.percentage}%"></div>`;
                    div.appendChild(bar);

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

                    header.addEventListener('click', () => {
                        partsContainer.style.display = partsContainer.style.display === 'none' ? 'block' : 'none';
                    });

                    container.appendChild(div);
                });
            } else {
                container.innerHTML = `<p>${data.message || 'No progress data available'}</p>`;
            }
        }

        function viewDocument(docId) {
            alert("Open inline viewer for document ID: " + docId);
        }

        // Initialize
        loadGroupHeader();
        loadGroupData();

        // Handle document upload
        // Fetch current group info
        document.getElementById('uploadDocBtn').addEventListener('click', () => {
            openUploadDocumentModal();
        });

        async function fetchMyGroup() {
            const res = await fetch('../queries/get_my_group.php');
            const data = await res.json();
            if(data.success) return data.group;
            else return null;
        }

        async function openUploadDocumentModal() {
            const group = await fetchMyGroup();
            if(!group) return Swal.fire('Error', 'No group assigned', 'error');

            const chapters = {
                1: ["Introduction", "Review of Related Literature", "Theoretical Framework and/or Conceptual frameworks", "Statement of the Problem, Hypotheses (if applicable)", "Scope and Delimitation of the study", "Significance of the study", "Definition of terms"],
                2: ["Research Design", "Research Locale", "Sample and sampling Procedure", "Sample and Sampling Criteria", "Data Gathering Procedure", "Data Gathering Instrument", "Data Analysis Techniques", "Ethical Considerations"],
                3: ["Planning phase","Simulation","Presentation of Results","Analysis and Interpretation of Results"],
                4: ["Summary of Findings","Conclusion","Limitation of the Study","Recommendations"]
            };

            let chapterOptions = Object.keys(chapters).map(ch => `<option value="${ch}">Chapter ${ch}</option>`).join('');
            Swal.fire({
                title: 'Upload Document',
                html: `
                    <div>
                        <label>Chapter</label>
                        <select id="swal-chapter" class="comment-textarea-custom">${chapterOptions}</select>
                    </div>
                    <div style="margin-top:10px;">
                        <label>Part</label>
                        <select id="swal-part" class="comment-textarea-custom"></select>
                    </div>
                    <div style="margin-top:10px;">
                        <label>Caption</label>
                        <input id="swal-title" type="text" class="comment-textarea-custom" placeholder="Document caption"/>
                    </div>
                    <div style="margin-top:10px;">
                        <input id="swal-file" type="file"/>
                    </div>
                `,
                showCancelButton: true,
                confirmButtonText: 'Upload',
                preConfirm: () => {
                    const chapter = document.getElementById('swal-chapter').value;
                    const part = document.getElementById('swal-part').value;
                    const title = document.getElementById('swal-title').value;
                    const file = document.getElementById('swal-file').files[0];
                    if(!chapter || !part || !file) Swal.showValidationMessage('All fields are required');
                    return {chapter, part, title, file};
                },
                didOpen: () => {
                    const chapterSelect = document.getElementById('swal-chapter');
                    const partSelect = document.getElementById('swal-part');

                    function updateParts() {
                        const ch = chapterSelect.value;
                        partSelect.innerHTML = chapters[ch].map(p => `<option value="${p}">${p}</option>`).join('');
                    }
                    updateParts();
                    chapterSelect.addEventListener('change', updateParts);
                }
            }).then(result => {
                if(result.isConfirmed){
                    const formData = new FormData();
                    formData.append('group_id', group.id);
                    formData.append('chapter', result.value.chapter);
                    formData.append('part', result.value.part);
                    formData.append('title', result.value.title);
                    formData.append('file', result.value.file);

                    fetch('../queries/upload_handler/upload_document.php', {
                        method: 'POST',
                        body: formData
                    })
                    .then(res => res.json())
                    .then(data => {
                        if(data.success){
                            Swal.fire({
                                icon: 'success',
                                title: 'Success',
                                text: 'Document uploaded successfully!',
                                timer: 2000
                            });

                            // Refresh progress and document list
                            if(currentGroupId) loadProgress(currentGroupId);
                            loadGroupData();
                        }else{
                            Swal.fire('Error', data.message, 'error');
                        }
                    })
                    .catch(err => {
                        Swal.fire('Error', 'Upload failed. Try again.', 'error');
                        console.error(err);
                    });
                }
            })
        }
    </script>
</html>