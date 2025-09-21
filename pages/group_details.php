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
                <a href="#" class="group-item active">
                    <div class="group-icon">AR</div>
                    <div>
                        <div>Alpha Research Team</div>
                        <div style="font-size: 12px; color: #65676b;">3 members</div>
                    </div>
                </a>
                <a href="#" class="group-item">
                    <div class="group-icon">BI</div>
                    <div>
                        <div>Beta Innovators</div>
                        <div style="font-size: 12px; color: #65676b;">4 members</div>
                    </div>
                </a>
            </nav>
        </aside>

        <!-- Main Content -->
        <main class="main-content">
            <div class="content-header">
                <h2>Submitted Documents</h2>
                <p class="content-subtitle">Review and comment on thesis documents submitted by group members</p>
            </div>

            <!-- Document Posts -->
            <div class="document-post">
                <div class="post-header">
                    <div class="post-author">
                        <div class="author-avatar">JD</div>
                        <div class="author-info">
                            <h4>John Doe</h4>
                            <div class="post-time">2 hours ago</div>
                        </div>
                    </div>
                    <div class="document-info">
                        <div class="document-icon">
                            <span class="icon icon-pdf"></span>
                        </div>
                        <div class="doc-details">
                            <h5>Thesis Draft 1.pdf</h5>
                            <div class="doc-meta">Chapter 1-3 • 2.1 MB • Last modified today</div>
                        </div>
                    </div>
                </div>
                <div class="post-description">
                    Here's the first draft of my thesis covering the introduction, literature review, and methodology sections. I've incorporated the feedback from our last meeting and would appreciate any additional comments, especially on the methodology approach.
                </div>
                <div class="post-actions">
                    <button class="action-btn">
                        <span class="icon icon-download"></span>
                        Download
                    </button>
                    <button class="action-btn">
                        <span class="icon icon-comment"></span>
                        Comment
                    </button>
                    <button class="action-btn">
                        <span class="icon icon-share"></span>
                        Share
                    </button>
                </div>
                <div class="comments-section">
                    <div class="comment-input">
                        <div class="comment-avatar">U</div>
                        <div class="comment-input-container">
                            <textarea class="comment-textarea" placeholder="Write a comment..." rows="1"></textarea>
                            <button class="comment-submit">
                                <span class="icon icon-send"></span>
                            </button>
                        </div>
                    </div>
                    <div class="existing-comments">
                        <div class="comment">
                            <div class="comment-bubble">
                                <div class="comment-author">Dr. Emily Carter</div>
                                <div class="comment-text">Great work on the methodology section! Consider adding more details about your sampling strategy.</div>
                            </div>
                            <div class="comment-time">1 hour ago</div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="document-post">
                <div class="post-header">
                    <div class="post-author">
                        <div class="author-avatar">JS</div>
                        <div class="author-info">
                            <h4>Jane Smith</h4>
                            <div class="post-time">5 hours ago</div>
                        </div>
                    </div>
                    <div class="document-info">
                        <div class="document-icon">
                            <span class="icon icon-word"></span>
                        </div>
                        <div class="doc-details">
                            <h5>Literature Review.docx</h5>
                            <div class="doc-meta">Chapter 2 • 1.8 MB • Last modified yesterday</div>
                        </div>
                    </div>
                </div>
                <div class="post-description">
                    Completed literature review focusing on AI applications in healthcare systems. I've reviewed 45 peer-reviewed articles from 2020-2024 and identified key research gaps that our study will address.
                </div>
                <div class="post-actions">
                    <button class="action-btn">
                        <span class="icon icon-download"></span>
                        Download
                    </button>
                    <button class="action-btn">
                        <span class="icon icon-comment"></span>
                        Comment
                    </button>
                    <button class="action-btn">
                        <span class="icon icon-share"></span>
                        Share
                    </button>
                </div>
                <div class="comments-section">
                    <div class="comment-input">
                        <div class="comment-avatar">U</div>
                        <div class="comment-input-container">
                            <textarea class="comment-textarea" placeholder="Write a comment..." rows="1"></textarea>
                            <button class="comment-submit">
                                <span class="icon icon-send"></span>
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <div class="document-post">
                <div class="post-header">
                    <div class="post-author">
                        <div class="author-avatar">MJ</div>
                        <div class="author-info">
                            <h4>Mike Johnson</h4>
                            <div class="post-time">1 day ago</div>
                        </div>
                    </div>
                    <div class="document-info">
                        <div class="document-icon">
                            <span class="icon icon-excel"></span>
                        </div>
                        <div class="doc-details">
                            <h5>Data Analysis.xlsx</h5>
                            <div class="doc-meta">Dataset & Analysis • 3.2 MB • Last modified 2 days ago</div>
                        </div>
                    </div>
                </div>
                <div class="post-description">
                    Preliminary data analysis results from our healthcare AI survey. The spreadsheet includes raw data, statistical analysis, and visualizations. Key findings show 78% improvement in diagnostic accuracy with AI assistance.
                </div>
                <div class="post-actions">
                    <button class="action-btn">
                        <span class="icon icon-download"></span>
                        Download
                    </button>
                    <button class="action-btn">
                        <span class="icon icon-comment"></span>
                        Comment
                    </button>
                    <button class="action-btn">
                        <span class="icon icon-share"></span>
                        Share
                    </button>
                </div>
                <div class="comments-section">
                    <div class="comment-input">
                        <div class="comment-avatar">U</div>
                        <div class="comment-input-container">
                            <textarea class="comment-textarea" placeholder="Write a comment..." rows="1"></textarea>
                            <button class="comment-submit">
                                <span class="icon icon-send"></span>
                            </button>
                        </div>
                    </div>
                    <div class="existing-comments">
                        <div class="comment">
                            <div class="comment-bubble">
                                <div class="comment-author">John Doe</div>
                                <div class="comment-text">Impressive results! Could you share the methodology for calculating the diagnostic accuracy improvement?</div>
                            </div>
                            <div class="comment-time">18 hours ago</div>
                        </div>
                        <div class="comment">
                            <div class="comment-bubble">
                                <div class="comment-author">Dr. Emily Carter</div>
                                <div class="comment-text">The statistical significance looks good. Make sure to include confidence intervals in the final analysis.</div>
                            </div>
                            <div class="comment-time">12 hours ago</div>
                        </div>
                    </div>
                </div>
            </div>
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
    </script>
</body>
</html>