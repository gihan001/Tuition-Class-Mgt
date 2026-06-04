# 1. Project Overview

## Project Name

**Smart Tuition Class Management System**

## Frontend Goal

Develop a clean, responsive, and beginner-friendly user interface for:

* Students
* Teachers
* Admins

using:

* HTML
* CSS
* JavaScript

The frontend will later connect with PHP and MySQL backend services.

---

# 2. Frontend Objectives

The frontend should:

* Provide an easy user experience
* Support desktop and mobile devices
* Allow navigation between modules
* Display data clearly
* Validate user input
* Prepare interfaces for backend integration

---

# 3. Technologies Used

| Technology   | Purpose                 |
| ------------ | ----------------------- |
| HTML5        | Page structure          |
| CSS3         | Styling and layouts     |
| JavaScript   | Interactivity           |
| Font Awesome | Icons                   |
| Google Fonts | Typography              |
| GitHub       | Version control         |
| VS Code      | Development environment |

---

# 4. Recommended Development Environment

## Software

### 1. Visual Studio Code

Use for:

* HTML editing
* CSS styling
* JavaScript coding

### 2. Browser

Recommended:

* Google Chrome

### 3. Live Server Extension

Used for:

* Real-time preview

---

# 5. Recommended VS Code Extensions

* Live Server
* HTML CSS Support
* Auto Rename Tag
* Prettier
* GitHub Copilot
* Path Intellisense

---

# 6. Frontend Folder Structure

```text id="q5r2ri"
frontend/
│
├── index.html
├── login.html
├── register.html
├── about.html
├── contact.html
│
├── admin/
│   ├── dashboard.html
│   ├── students.html
│   ├── teachers.html
│   ├── classes.html
│   ├── payments.html
│   └── notices.html
│
├── student/
│   ├── dashboard.html
│   ├── classes.html
│   ├── timetable.html
│   └── notices.html
│
├── teacher/
│   ├── dashboard.html
│   ├── students.html
│   ├── materials.html
│   └── classes.html
│
├── assets/
│   │
│   ├── css/
│   │   ├── style.css
│   │   ├── dashboard.css
│   │   ├── forms.css
│   │   └── responsive.css
│   │
│   ├── js/
│   │   ├── script.js
│   │   ├── validation.js
│   │   ├── dashboard.js
│   │   └── table-search.js
│   │
│   └── images/
│
└── components/
    ├── navbar.html
    ├── sidebar.html
    └── footer.html
```

---

# 7. Frontend Architecture

## Frontend Layers

| Layer            | Purpose       |
| ---------------- | ------------- |
| HTML Layer       | Structure     |
| CSS Layer        | Design        |
| JavaScript Layer | Functionality |

---

# 8. User Interface Design

# Design Principles

The UI should be:

* Simple
* Clean
* Responsive
* Beginner-friendly
* Easy to navigate

---

# 9. Color Palette

| Purpose    | Color   |
| ---------- | ------- |
| Primary    | #2563EB |
| Secondary  | #FFFFFF |
| Background | #F5F5F5 |
| Success    | #22C55E |
| Danger     | #EF4444 |
| Dark Text  | #1F2937 |

---

# 10. Typography

## Recommended Fonts

* Poppins
* Roboto
* Open Sans

Example:

```html id="6p06fh"
<link href="https://fonts.googleapis.com/css2?family=Poppins&display=swap" rel="stylesheet">
```

---

# 11. Core Frontend Modules

# Public Module

## Pages

| Page          | Purpose             |
| ------------- | ------------------- |
| index.html    | Home page           |
| login.html    | User login          |
| register.html | User registration   |
| about.html    | About system        |
| contact.html  | Contact information |

---

# Admin Module

## Pages

| Page           | Purpose         |
| -------------- | --------------- |
| dashboard.html | Admin overview  |
| students.html  | Manage students |
| teachers.html  | Manage teachers |
| classes.html   | Manage classes  |
| payments.html  | Manage payments |
| notices.html   | Publish notices |

---

# Student Module

| Page           | Purpose            |
| -------------- | ------------------ |
| dashboard.html | Student dashboard  |
| classes.html   | View classes       |
| timetable.html | View timetable     |
| notices.html   | View announcements |

---

# Teacher Module

| Page           | Purpose            |
| -------------- | ------------------ |
| dashboard.html | Teacher dashboard  |
| students.html  | View student lists |
| materials.html | Upload materials   |
| classes.html   | Assigned classes   |

---

# 12. Page Development Details

# Home Page

## Sections

* Hero section
* Features section
* About section
* Contact section
* Footer

---

# Login Page

## Components

* Email field
* Password field
* Login button
* Forgot password link

---

# Dashboard Pages

## Common Components

* Sidebar
* Top navigation
* Statistics cards
* Tables
* Notifications

---

# 13. CSS Documentation

# CSS Structure

## style.css

Contains:

* Global styles
* Typography
* Buttons
* Layout styles

---

## dashboard.css

Contains:

* Sidebar styles
* Dashboard layouts
* Cards
* Tables

---

## forms.css

Contains:

* Form styles
* Input styling
* Validation styles

---

## responsive.css

Contains:

* Media queries
* Mobile layouts
* Tablet support

---

# 14. CSS Development Standards

## Naming Convention

Example:

```css id="a63i7l"
.dashboard-container
.student-table
.login-form
.primary-button
```

---

# 15. Responsive Design

# Screen Support

| Device  | Width          |
| ------- | -------------- |
| Mobile  | < 768px        |
| Tablet  | 768px – 1024px |
| Desktop | > 1024px       |

---

# Example Media Query

```css id="g6cwgm"
@media(max-width:768px){
    .sidebar{
        display:none;
    }
}
```

---

# 16. JavaScript Documentation

# JavaScript Responsibilities

* Form validation
* Sidebar toggle
* Table search
* Notifications
* Dynamic interactions

---

# validation.js

## Responsibilities

* Validate login forms
* Validate registration forms
* Prevent empty inputs

Example:

```javascript id="mxx8h7"
function validateLogin(){
    let email = document.getElementById("email").value;

    if(email === ""){
        alert("Email required");
        return false;
    }

    return true;
}
```

---

# dashboard.js

## Responsibilities

* Sidebar toggle
* Dashboard interactions

Example:

```javascript id="w8ns3f"
function toggleSidebar(){
    document.querySelector(".sidebar").classList.toggle("active");
}
```

---

# table-search.js

## Responsibilities

* Search student tables
* Filter payments
* Search classes

---

# 17. Reusable Components

# Components

| Component | Purpose              |
| --------- | -------------------- |
| Navbar    | Main navigation      |
| Sidebar   | Dashboard navigation |
| Footer    | Footer section       |
| Cards     | Statistics display   |
| Tables    | Data display         |
| Forms     | User input           |

---

# 18. Frontend Workflow

# Step 1 — Setup

Tasks:

* Create folder structure
* Install VS Code extensions
* Create GitHub repository

---

# Step 2 — Static UI Development

Develop:

* Home page
* Login page
* Register page

---

# Step 3 — Dashboard Development

Develop:

* Admin dashboard
* Student dashboard
* Teacher dashboard

---

# Step 4 — Form Development

Develop:

* Student forms
* Teacher forms
* Payment forms

---

# Step 5 — JavaScript Features

Add:

* Validation
* Search
* Responsive sidebar

---

# Step 6 — Responsive Design

Optimize:

* Mobile layouts
* Tablet layouts

---

# Step 7 — Frontend Testing

Test:

* Navigation
* Responsive behavior
* Form validation

---

# 19. Frontend Development Timeline

| Week   | Tasks                          |
| ------ | ------------------------------ |
| Week 1 | Setup + Public pages           |
| Week 2 | Dashboards                     |
| Week 3 | Forms + Tables                 |
| Week 4 | JavaScript + Responsive Design |

---

# 20. GitHub Collaboration Plan

| Member   | Responsibilities        |
| -------- | ----------------------- |
| Member 1 | Home page + CSS         |
| Member 2 | Dashboards              |
| Member 3 | Forms                   |
| Member 4 | JavaScript              |
| Member 5 | Testing + Documentation |

---

# 21. UI Best Practices

## Recommended

* Use consistent colors
* Use proper spacing
* Keep layouts simple
* Use readable fonts
* Maintain responsive design

---

## Avoid

* Complex animations
* Overloaded pages
* Too many colors
* Large images
* Complicated navigation

---

# 22. Frontend Testing Checklist

## Functional Testing

| Test             | Status |
| ---------------- | ------ |
| Navigation works | ☐      |
| Forms work       | ☐      |
| Search works     | ☐      |
| Buttons work     | ☐      |

---

## Responsive Testing

| Device  | Status |
| ------- | ------ |
| Mobile  | ☐      |
| Tablet  | ☐      |
| Desktop | ☐      |

---

# 23. Final Frontend Deliverables

The frontend should finally include:

✅ Complete HTML pages
✅ Responsive CSS
✅ JavaScript functionality
✅ Dashboard UI
✅ Forms and tables
✅ Navigation system
✅ Reusable components
✅ Clean folder structure

---

# 24. Future Backend Integration

After frontend completion:

## Convert HTML to PHP

Example:

```text id="jlwmrh"
login.html → login.php
```

---

## Connect Backend

Later integrate:

* PHP
* MySQL
* Authentication
* CRUD operations
* Session management

---

# 25. Final Recommendation

For beginners, this frontend-first method is the best because:

* Easier to learn
* Easier debugging
* Faster development
* Better teamwork
* Cleaner project structure

This approach also matches your course requirements using:

* HTML
* CSS
* JavaScript
* PHP 
