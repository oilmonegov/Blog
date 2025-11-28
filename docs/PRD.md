# Product Requirements Document (PRD)
## Simple Blog System

### 1. Project Overview

A simple, modern blog system built with Laravel that allows administrators and authors to create, manage, and publish blog posts with comments functionality.

### 2. Technology Stack

- **Backend Framework**: Laravel (Latest Stable Version - 12.x)
- **Database**: MySQL
- **Cache/Sessions**: Redis
- **Frontend**: Blade Templates
- **Styling**: TailwindCSS
- **Testing**: PestPHP (Feature, Unit, Arch, Behavioral, Property)
- **Static Analysis**: Larastan
- **Code Style**: Laravel Pint

### 3. Core Features

#### 3.1 Authentication & Authorization
- User registration and login system
- Two user roles:
  - **Admin**: Full system access, can manage all posts, comments, categories, and users
  - **Author**: Can create, edit, and delete their own posts; can moderate comments on their posts
- Role-based access control using Laravel Policies

#### 3.2 Post Management
- **Post Fields**:
  - Title (required)
  - Slug (auto-generated from title, unique)
  - Content (required, rich text)
  - Excerpt (auto-generated from content, configurable length)
  - Status: Draft or Published (enum)
  - Author (assigned automatically)
  - Published Date (set when status changes to Published)
  - Meta Title (for SEO)
  - Meta Description (for SEO)
- **Post Statuses**:
  - Draft: Saved but not visible to public
  - Published: Visible to public
- **Post Permissions**:
  - Authors can create, edit, and delete their own posts
  - Admins can manage all posts
  - Posts can be saved as drafts before publishing
- **Post Relationships**:
  - Belongs to Author (User)
  - Belongs to many Categories
  - Belongs to many Tags
  - Has many Comments

#### 3.3 Category Management
- Categories are optional
- Posts can have multiple categories
- Category fields:
  - Name (required, unique)
  - Slug (auto-generated from name)
  - Description (optional)
- Categories can be created and managed by Admins
- Public category archive pages

#### 3.4 Tag Management
- Tags are optional
- Posts can have multiple tags
- Tag fields:
  - Name (required, unique)
  - Slug (auto-generated from name)
- Tags are created automatically when added to posts
- Public tag archive pages

#### 3.5 Comment System
- Comments require user authentication (logged-in users only)
- Comments are auto-approved (no moderation queue)
- Comment fields:
  - Content (required)
  - Post (required)
  - User (author of comment)
  - Approved At (timestamp, set automatically)
- **Comment Permissions**:
  - Any authenticated user can comment
  - Authors can delete comments on their own posts
  - Admins can delete any comment
- Rate limiting applied to comment submissions

#### 3.6 Public Pages
- **Blog Listing Page**:
  - Displays published posts (latest first)
  - Pagination (10 posts per page)
  - Post preview with title, excerpt, author, date, categories, tags
- **Individual Post Page**:
  - Full post content
  - Author information
  - Publication date
  - Categories and tags
  - Comments section (with form for authenticated users)
  - SEO meta tags
- **Category Archive Page**:
  - Lists all posts in a specific category
  - Pagination (10 posts per page)
- **Tag Archive Page**:
  - Lists all posts with a specific tag
  - Pagination (10 posts per page)

#### 3.7 Admin Dashboard
- Separate admin area for content management
- **Post Management**:
  - List all posts (with filters for status, author)
  - Create new post
  - Edit existing post
  - Delete post (soft delete)
  - Publish/unpublish posts
- **Comment Management**:
  - List all comments
  - Delete comments
- **Category Management**:
  - List all categories
  - Create category
  - Edit category
  - Delete category

### 4. Technical Requirements

#### 4.1 Database Schema

**users** (extends Laravel default)
- id
- name
- email
- email_verified_at
- password
- role (enum: admin, author)
- remember_token
- timestamps

**posts**
- id
- title
- slug (unique)
- content (text)
- excerpt (text)
- status (enum: draft, published)
- author_id (foreign key to users)
- published_at (nullable timestamp)
- meta_title (nullable)
- meta_description (nullable)
- deleted_at (soft deletes)
- timestamps

**categories**
- id
- name (unique)
- slug (unique)
- description (nullable text)
- timestamps

**tags**
- id
- name (unique)
- slug (unique)
- timestamps

**comments**
- id
- content (text)
- post_id (foreign key to posts)
- user_id (foreign key to users)
- approved_at (timestamp)
- deleted_at (soft deletes)
- timestamps

**category_post** (pivot)
- category_id
- post_id

**post_tag** (pivot)
- post_id
- tag_id

#### 4.2 Caching Strategy (Redis)
- Cache published posts (with TTL)
- Cache post counts by category/tag
- Cache user sessions
- Cache queue jobs
- Cache invalidation on post create/update/delete

#### 4.3 SEO Requirements
- Meta title and description for each post
- SEO-friendly URLs (slugs)
- Proper heading hierarchy
- Open Graph meta tags (optional enhancement)

### 5. User Stories

#### 5.1 Admin User Stories
- As an admin, I want to create and manage blog posts so that I can publish content
- As an admin, I want to manage categories so that I can organize content
- As an admin, I want to moderate comments so that I can maintain content quality
- As an admin, I want to view all posts regardless of author so that I can manage the entire blog

#### 5.2 Author User Stories
- As an author, I want to create blog posts so that I can contribute content
- As an author, I want to save drafts so that I can work on posts over time
- As an author, I want to edit my own posts so that I can improve content
- As an author, I want to publish my posts so that readers can see them
- As an author, I want to manage comments on my posts so that I can engage with readers

#### 5.3 Public User Stories
- As a reader, I want to view blog posts so that I can read content
- As a reader, I want to browse posts by category so that I can find related content
- As a reader, I want to browse posts by tag so that I can discover content
- As a reader, I want to comment on posts so that I can engage with authors
- As a reader, I want to see recent posts so that I can stay updated

### 6. Non-Functional Requirements

#### 6.1 Performance
- Page load time < 2 seconds
- Efficient database queries (N+1 prevention)
- Redis caching for frequently accessed data

#### 6.2 Security
- CSRF protection on all forms
- XSS protection (sanitized content)
- SQL injection prevention (Eloquent ORM)
- Rate limiting on comment submissions
- Role-based authorization

#### 6.3 Code Quality
- 100% test coverage target
- Larastan level 5 (strictest)
- PSR-12 code style compliance
- No code duplication
- SOLID principles adherence

#### 6.4 Browser Support
- Modern browsers (Chrome, Firefox, Safari, Edge - last 2 versions)
- Responsive design (mobile, tablet, desktop)

### 7. Out of Scope

- Search functionality
- Media/image uploads
- WYSIWYG editor (basic textarea, can be enhanced later)
- Email notifications
- RSS feeds
- Social media integration
- Multi-language support
- Post scheduling
- Comment moderation queue
- Guest comments

### 8. Future Enhancements (Post-MVP)

- Full-text search
- Image uploads and media library
- Rich text editor (TinyMCE/CKEditor)
- Email notifications for new comments
- RSS feed generation
- Post scheduling
- Advanced analytics
- SEO tools integration

### 9. Success Criteria

- All core features implemented and tested
- 100% test coverage achieved
- Larastan level 5 passed
- All user stories completed
- Performance benchmarks met
- Security audit passed
- Code review approved

### 10. Timeline & Milestones

1. **Phase 1**: Project setup, database schema, models
2. **Phase 2**: Authentication, authorization, policies
3. **Phase 3**: Post management (CRUD)
4. **Phase 4**: Categories and tags
5. **Phase 5**: Comments system
6. **Phase 6**: Public frontend
7. **Phase 7**: Admin dashboard
8. **Phase 8**: Testing and optimization
9. **Phase 9**: Documentation and deployment

---

**Document Version**: 1.0  
**Last Updated**: 2024  
**Status**: Draft

