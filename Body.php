 
 <!-- <div class="pre-loader">
		<div class="pre-loader-box">
			<div class="loader-logo">
				<img src="vendors/images/deskapp-logo.svg" alt="" />
			</div>
			<div class="loader-progress" id="progress_div">
				<div class="bar" id="bar1"></div>
			</div>
			<div class="percent" id="percent1">0%</div>
			<div class="loading-text">Loading...</div>
		</div>
	</div> -->

    <div class="header">
        <div class="header-left">
            <div class="menu-icon bi bi-list"></div>
        </div>
        <div class="header-right">
            <div class="user-info-dropdown">
                <div class="dropdown">
                    <a class="dropdown-toggle" href="#" role="button" data-toggle="dropdown">
                        <span class="user-icon">
                            <!-- <img src="vendors/images/photo1.jpg" alt="" /> --><i class="icon-copy fa fa-user-circle-o fa-2x" aria-hidden="true"></i>
                        </span>
                        <span class="user-name">
                            <h4 class="text-blue"><?php echo $_SESSION['UserName'];?></h4>
                            <h6><?php echo $_SESSION['Branch'];?></h6>  
                        </span>
                    </a>
                    <div class="dropdown-menu dropdown-menu-right dropdown-menu-icon-list">
                        <a class="dropdown-item" href="javascript:;" data-toggle="right-sidebar"><i
                                class="dw dw-settings2"></i> Settings</a>
                        <a class="dropdown-item" href="logout.php"><i class="dw dw-logout"></i> Log Out</a>
                    </div>
                </div>
            </div>
            <div class="github-link">
                <a href="https://github.com/Siri-Vennela-K" target="_blank"><img src="vendors/images/github.svg"
                        alt="" /></a>
            </div>
        </div>

    </div>

    <div class="right-sidebar">
        <div class="sidebar-title">
            <h3 class="weight-600 font-16 text-blue">
                Layout Settings
                <span class="btn-block font-weight-400 font-12">User Interface Settings</span>
            </h3>
            <div class="close-sidebar" data-toggle="right-sidebar-close">
                <i class="icon-copy ion-close-round"></i>
            </div>
        </div>
        <div class="right-sidebar-body customscroll">
            <div class="right-sidebar-body-content">
                <h4 class="weight-600 font-18 pb-10">Header Background</h4>
                <div class="sidebar-btn-group pb-30 mb-10">
                    <a href="javascript:void(0);" class="btn btn-outline-primary header-white active">White</a>
                    <a href="javascript:void(0);" class="btn btn-outline-primary header-dark">Dark</a>
                </div>

                <h4 class="weight-600 font-18 pb-10">Sidebar Background</h4>
                <div class="sidebar-btn-group pb-30 mb-10">
                    <a href="javascript:void(0);" class="btn btn-outline-primary sidebar-light">White</a>
                    <a href="javascript:void(0);" class="btn btn-outline-primary sidebar-dark active">Dark</a>
                </div>

                <h4 class="weight-600 font-18 pb-10">Menu Dropdown Icon</h4>
                <div class="sidebar-radio-group pb-10 mb-10">
                    <div class="custom-control custom-radio custom-control-inline">
                        <input type="radio" id="sidebaricon-1" name="menu-dropdown-icon" class="custom-control-input"
                            value="icon-style-1" checked="" />
                        <label class="custom-control-label" for="sidebaricon-1"><i class="fa fa-angle-down"></i></label>
                    </div>
                    <div class="custom-control custom-radio custom-control-inline">
                        <input type="radio" id="sidebaricon-2" name="menu-dropdown-icon" class="custom-control-input"
                            value="icon-style-2" />
                        <label class="custom-control-label" for="sidebaricon-2"><i class="ion-plus-round"></i></label>
                    </div>
                    <div class="custom-control custom-radio custom-control-inline">
                        <input type="radio" id="sidebaricon-3" name="menu-dropdown-icon" class="custom-control-input"
                            value="icon-style-3" />
                        <label class="custom-control-label" for="sidebaricon-3"><i
                                class="fa fa-angle-double-right"></i></label>
                    </div>
                </div>

                <h4 class="weight-600 font-18 pb-10">Menu List Icon</h4>
                <div class="sidebar-radio-group pb-30 mb-10">
                    <div class="custom-control custom-radio custom-control-inline">
                        <input type="radio" id="sidebariconlist-1" name="menu-list-icon" class="custom-control-input"
                            value="icon-list-style-1" checked="" />
                        <label class="custom-control-label" for="sidebariconlist-1"><i
                                class="ion-minus-round"></i></label>
                    </div>
                    <div class="custom-control custom-radio custom-control-inline">
                        <input type="radio" id="sidebariconlist-2" name="menu-list-icon" class="custom-control-input"
                            value="icon-list-style-2" />
                        <label class="custom-control-label" for="sidebariconlist-2"><i class="fa fa-circle-o"
                                aria-hidden="true"></i></label>
                    </div>
                    <div class="custom-control custom-radio custom-control-inline">
                        <input type="radio" id="sidebariconlist-3" name="menu-list-icon" class="custom-control-input"
                            value="icon-list-style-3" />
                        <label class="custom-control-label" for="sidebariconlist-3"><i class="dw dw-check"></i></label>
                    </div>
                    <div class="custom-control custom-radio custom-control-inline">
                        <input type="radio" id="sidebariconlist-4" name="menu-list-icon" class="custom-control-input"
                            value="icon-list-style-4" checked="" />
                        <label class="custom-control-label" for="sidebariconlist-4"><i
                                class="icon-copy dw dw-next-2"></i></label>
                    </div>
                    <div class="custom-control custom-radio custom-control-inline">
                        <input type="radio" id="sidebariconlist-5" name="menu-list-icon" class="custom-control-input"
                            value="icon-list-style-5" />
                        <label class="custom-control-label" for="sidebariconlist-5"><i
                                class="dw dw-fast-forward-1"></i></label>
                    </div>
                    <div class="custom-control custom-radio custom-control-inline">
                        <input type="radio" id="sidebariconlist-6" name="menu-list-icon" class="custom-control-input"
                            value="icon-list-style-6" />
                        <label class="custom-control-label" for="sidebariconlist-6"><i class="dw dw-next"></i></label>
                    </div>
                </div>

                <div class="reset-options pt-30 text-center">
                    <button class="btn btn-danger" id="reset-settings">
                        Reset Settings
                    </button>
                </div>
            </div>
        </div>
    </div>

    <div class="left-side-bar">
        <div class="brand-logo">
            <a href="logout.php">
                <img src="vendors/images/rgukt-logo.png" alt="" /><b>RGUKT</b>
            </a>
            <div class="close-sidebar" data-toggle="left-sidebar-close">
                <i class="ion-close-round"></i>
            </div>
        </div>
        <div class="menu-block customscroll">
            <div class="sidebar-menu">
      

<ul id="accordion-menu">
    <?php if ($userRole === 'Admin' || $userRole === 'Faculty'): ?>
        <li class="Faculty">
            <a href="AdminDashboard.php" class="dropdown-toggle no-arrow">
                <span class="micon bi bi-diagram-3"></span><span class="mtext">Faculty Dashboard</span>
            </a>
        </li>
        <li class="Faculty">
            <a href="requests.php" class="dropdown-toggle no-arrow">
                <span class="micon bi bi-chat-right-dots"></span><span class="mtext">Requests History</span>
            </a>
        </li>
    <?php endif; ?>

    <?php if ($userRole === 'Admin'): ?>
        <li class="Admin">
            <a href="Addfaculty.php" class="dropdown-toggle no-arrow">
                <span class="micon bi bi-person-plus-fill"></span><span class="mtext">Add Faculty</span>
            </a>
        </li>
    <?php endif; ?>

    <?php if ($userRole === 'Admin' || $userRole === 'Faculty'): ?>
        <li class="Faculty">
            <a href="register.php" class="dropdown-toggle no-arrow">
                <span class="micon bi bi-person-plus-fill"></span><span class="mtext">Add Student</span>
            </a>
        </li>
        <li class="Faculty">
            <a href="studentsdata.php" class="dropdown-toggle no-arrow">
                <span class="micon bi bi-person-video2"></span><span class="mtext">Student Data</span>
            </a>
        </li>
    <?php endif; ?>

    <?php if ($userRole === 'Student'): ?>
        <li class="Student">
            <a href="studentdashboard.php" class="dropdown-toggle no-arrow">
                <span class="micon bi bi-diagram-3"></span><span class="mtext">Student Dashboard</span>
            </a>
        </li>
        <li class="Student">
            <a href="studentrequests.php" class="dropdown-toggle no-arrow">
                <span class="micon bi bi-chat-right-dots"></span><span class="mtext">Requests History</span>
            </a>
        </li>
    <?php endif; ?>
</ul>

            </div>
        </div>
    </div>
    <div class="mobile-menu-overlay"></div>