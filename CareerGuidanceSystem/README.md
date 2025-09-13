<h1>Career Guidance System</h1>

<h2>What This Project Is About</h2>
<p>
This project is a website designed to help people find the best career path for them. It's like a smart guide that learns about you – your skills, what you like, and how you prefer to work – and then gives you personalized job suggestions. It can even help you create a basic resume!
</p>

<p>The website has two types of users:</p>
<ul>
  <li><strong>Regular Users:</strong> People who want career advice.</li>
  <li><strong>Administrators (Admins):</strong> People who manage the website's content, like adding new jobs or skills.</li>
</ul>

<h2>What It Can Do (Key Features)</h2>
<ul>
  <li><strong>Easy Sign-Up and Login:</strong> Create an account and log in easily.</li>
  <li><strong>Separate Admin Login:</strong> Admins have their own login page for security.</li>
  <li><strong>Your Personal Profile:</strong> Update your:
    <ul>
      <li>School/university name and graduation year</li>
      <li>Major and GPA</li>
    </ul>
  </li>
  <li><strong>Lots of Skills:</strong> Choose from many computer-related skills like Python, Java, Cloud, AI, etc., with a skill search bar.</li>
  <li><strong>Detailed Quiz:</strong> Questions about work environment, problem-solving, interests, and personality.</li>
  <li><strong>Work Experience Summary:</strong> Add your past job experiences.</li>
  <li><strong>Projects Summary:</strong> Describe your previous projects.</li>
  <li><strong>Smart Job Suggestions:</strong>
    <ul>
      <li>Top Career Paths with a "Compatibility Score"</li>
      <li>Suggested Skills to Learn</li>
      <li>Success Prediction Percentage</li>
    </ul>
  </li>
  <li><strong>Quick Resume Builder:</strong> Instantly generate and download a basic resume as PDF.</li>
  <li><strong>Admin Control Panel:</strong> Admins can:
    <ul>
      <li>Manage Careers (Add/Edit/Delete)</li>
      <li>Manage Skills (Add/Edit/Delete)</li>
      <li>Manage Users (View/Delete)</li>
    </ul>
  </li>
</ul>

<h2>What It's Built With (Technologies)</h2>
<ul>
  <li><strong>Frontend:</strong>
    <ul>
      <li><code>HTML</code> – Webpage structure</li>
      <li><code>CSS</code> – Styling</li>
      <li><code>JavaScript</code> – Interactivity</li>
    </ul>
  </li>
  <li><strong>Backend:</strong>
    <ul>
      <li><code>PHP</code> – Server-side logic and algorithms</li>
    </ul>
  </li>
  <li><strong>Database:</strong>
    <ul>
      <li><code>MySQL</code> – Data storage</li>
    </ul>
  </li>
</ul>

<h2>Project Files (Structure)</h2>
<pre>
CareerGuidanceSystem/
├── admin/                     # Files for the Admin Panel
│   ├── dashboard.php          # Admin's main page
│   ├── manage_careers.php     # Add/Edit/Delete job careers
│   ├── manage_skills.php      # Add/Edit/Delete skills
│   └── manage_users.php       # View and delete user accounts
├── algorithms/                # PHP files for the smart recommendation logic
│   ├── association_rule_mining.php # Suggests new skills
│   ├── career_scoring.php     # Calculates job compatibility scores
│   └── linear_regression.php  # Predicts success percentages
├── assets/
│   ├── css/
│   │   └── style.css          # Main styles
│   └── js/
│       └── script.js          # JavaScript functionality
├── backend/
│   ├── process_admin_login.php
│   ├── process_login.php
│   └── process_registration.php
├── config/
│   └── config.php             # DB connection
├── user/
│   ├── dashboard.php
│   ├── generate_resume.php
│   ├── profile.php
│   └── recommendations.php
├── admin_login.php
├── index.php
├── login.php
├── logout.php
├── register.php
├── README.md
└── database_schema.sql
</pre>
