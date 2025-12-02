# 🛡️ SecureWatch - Security Monitoring Dashboard

A comprehensive security monitoring dashboard built with Symfony 6.4, designed for real-time security event tracking, alert management, and incident response.

## ✨ Features

### 🔍 **Dynamic Search**
- Real-time search across Events, Alerts, Incidents, and Assets
- Debounced input with 300ms delay for performance
- Rich search results with color-coded types and severity indicators
- Support for multiple field searches (title, description, source, IP addresses)

### 🎨 **Theme System**
- Complete light/dark mode support
- Theme persistence across browser sessions
- Quick toggle in navigation bar
- Comprehensive CSS styling for both themes
- Cross-page theme synchronization

### 🔎 **Advanced Filtering**
- **Events**: Filter by severity and source
- **Alerts**: Filter by severity and status
- **Incidents**: Filter by severity and status
- Real-time filter application with URL state management
- Clear filters functionality

### 📊 **Dashboard Overview**
- Real-time statistics for events, alerts, and incidents
- Recent activity monitoring
- System status indicators
- Interactive data visualization

### 👥 **User Management**
- Role-based access control (ROLE_USER, ROLE_ADMIN)
- User authentication and authorization
- Profile management
- Admin-only user management interface

### 🚨 **Alert System**
- Multi-severity alert levels (critical, high, medium, low)
- Real-time notifications
- Alert lifecycle management
- Integration with incident workflow

### 📋 **Incident Management**
- Complete incident lifecycle tracking
- Status management (open, in progress, resolved, closed)
- Assignment and notes functionality
- Timeline tracking

### 💻 **Asset Inventory**
- IT asset tracking and management
- IP address management
- Asset categorization
- Relationship mapping to security events

## 🛠️ Technology Stack

- **Backend**: Symfony 6.4
- **Frontend**: Twig templates with Tailwind CSS
- **Database**: PostgreSQL with Doctrine ORM
- **JavaScript**: Alpine.js for dynamic interactions
- **Authentication**: Symfony Security Component
- **Containerization**: Docker & Docker Compose

## 📦 Installation

### Prerequisites
- PHP 8.2+
- PostgreSQL 14+
- Composer
- Docker & Docker Compose (optional)

### Setup Instructions

1. **Clone the repository**
   ```bash
   git clone git@github.com:AnisDh25/SecureWatch.git
   cd SecureWatch
   ```

2. **Install dependencies**
   ```bash
   composer install
   ```

3. **Environment Configuration**
   ```bash
   cp .env .env.local
   # Edit .env.local with your database credentials
   ```

4. **Database Setup**
   ```bash
   # Create database
   php bin/console doctrine:database:create
   
   # Run migrations
   php bin/console doctrine:migrations:migrate
   
   # Load sample data (optional)
   php bin/console doctrine:fixtures:load
   ```

5. **Start Development Server**
   ```bash
   php bin/console server:start
   ```

6. **Access Application**
   - URL: `http://127.0.0.1:8000`
   - Default Admin: `admin@securewatch.com` / `password`

### Docker Setup

```bash
# Start all services
docker-compose up -d

# Run database migrations
docker-compose exec php php bin/console doctrine:migrations:migrate

# Load fixtures
docker-compose exec php php bin/console doctrine:fixtures:load
```

## 🏗️ Project Structure

```
SecureWatch/
├── src/
│   ├── Controller/          # HTTP Controllers
│   ├── Entity/             # Doctrine Entities
│   ├── Repository/          # Data Repositories
│   ├── Form/               # Form Types
│   └── DataFixtures/       # Sample Data
├── templates/              # Twig Templates
│   ├── dashboard/          # Dashboard Layouts
│   ├── security/           # Authentication Pages
│   ├── event/              # Event Management
│   ├── alert/              # Alert Management
│   ├── incident/           # Incident Management
│   └── asset/              # Asset Management
├── config/                 # Symfony Configuration
├── migrations/             # Database Migrations
└── public/                 # Web Assets
```

## 🎯 Core Features

### Search System
- **Multi-entity search**: Events, Alerts, Incidents, Assets
- **Real-time results**: Alpine.js with debounced input
- **Rich formatting**: Color-coded by type and severity
- **Performance optimized**: 10-result limit with ranking

### Theme System
- **Light/Dark modes**: Complete CSS coverage
- **Persistence**: localStorage integration
- **Accessibility**: High contrast support
- **Responsive**: Works across all device sizes

### Filtering System
- **Dynamic filters**: Real-time application
- **URL state**: Bookmarkable filter states
- **Multi-field**: Support for complex queries
- **User-friendly**: Clear filter options

## 📊 Database Schema

### Core Entities
- **User**: Authentication and role management
- **Event**: Security event tracking
- **Alert**: Security alerts with severity
- **Incident**: Incident management workflow
- **Asset**: IT asset inventory
- **Notification**: User notifications
- **AlertRule**: Automated alert rules

### Relationships
- Users can manage incidents and alerts
- Events can trigger alerts
- Alerts can be grouped into incidents
- Assets can be associated with events and alerts

## 🔐 Security Features

- **Authentication**: Symfony Security Component
- **Authorization**: Role-based access control
- **CSRF Protection**: Built-in Symfony protection
- **XSS Prevention**: Twig auto-escaping
- **SQL Injection Prevention**: Doctrine parameter binding

## 🚀 API Endpoints

### Search API
```
GET /search?q={query}
```
Returns JSON results for multi-entity search

### Authentication
```
POST /login
POST /logout
```

### Entity Management
```
GET /events        # List events with filtering
GET /alerts        # List alerts with filtering
GET /incidents     # List incidents with filtering
GET /assets        # List assets
```

## 🎨 Customization

### Adding New Entities
1. Create Entity class in `src/Entity/`
2. Create Repository class in `src/Repository/`
3. Create Controller in `src/Controller/`
4. Add Twig templates in `templates/`
5. Update routing configuration

### Customizing Themes
- Edit CSS in `templates/base.html.twig`
- Add new theme variants in the `<style>` block
- Update JavaScript theme switching logic

### Extending Search
- Add `findBySearchQuery()` method to repositories
- Update `SearchController` to include new entity types
- Add search result formatting in frontend

## 🧪 Testing

### Running Tests
```bash
# Run all tests
php bin/console phpunit

# Run specific test
php bin/console phpunit tests/Controller/SearchControllerTest.php
```

### Testing Features
- Use `search-test.html` for standalone search testing
- Use `theme-test.html` for theme switcher testing
- Access `/phpinfo.php` for environment verification

## 📝 Development

### Code Style
- Follow PSR-12 coding standards
- Use meaningful commit messages
- Include documentation for new features

### Adding Features
1. Create feature branch
2. Implement functionality
3. Add tests
4. Update documentation
5. Submit pull request

### Debugging
- Use Symfony's built-in debug toolbar
- Check logs in `var/log/dev.log`
- Use `php bin/console debug:config` for configuration

## 🔄 Version History

### v1.0.0 (Current)
- ✅ Initial Symfony 6.4 setup
- ✅ Dynamic search functionality
- ✅ Advanced filtering system
- ✅ Light/dark theme switcher
- ✅ Complete CRUD operations
- ✅ User authentication system

## 🤝 Contributing

1. Fork the repository
2. Create feature branch (`git checkout -b feature/amazing-feature`)
3. Commit changes (`git commit -m 'feat: Add amazing feature'`)
4. Push to branch (`git push origin feature/amazing-feature`)
5. Open Pull Request

## 📄 License

This project is licensed under the MIT License - see the LICENSE file for details.

## 👥 Author

**Anis Dhahoui**
- GitHub: [@AnisDh25](https://github.com/AnisDh25)
- Email: anis.dhaoui@tek-up.de

## 🙏 Acknowledgments

- Symfony Framework for robust backend foundation
- Tailwind CSS for beautiful UI components
- Alpine.js for lightweight frontend interactions
- PostgreSQL for reliable data storage

---

**🛡️ SecureWatch - Your Security Operations Center**
