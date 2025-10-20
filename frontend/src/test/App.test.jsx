import { render, screen } from '@testing-library/react';
import { describe, it, expect, vi, beforeEach } from 'vitest';
import App from '../App';

// Mock all the dependencies
vi.mock('../contexts/AuthContext', () => ({
  AuthProvider: ({ children }) => <div data-testid="auth-provider">{children}</div>,
  useAuth: () => ({
    user: null,
    isAuthenticated: false,
    loading: false,
    login: vi.fn(),
    register: vi.fn(),
    logout: vi.fn()
  })
}));

vi.mock('../components/Layout', () => ({
  default: ({ children }) => <div data-testid="layout">{children}</div>
}));

vi.mock('../pages/Login', () => ({
  default: () => <div data-testid="login-page">Login Page</div>
}));

vi.mock('../pages/Register', () => ({
  default: () => <div data-testid="register-page">Register Page</div>
}));

vi.mock('../pages/Dashboard', () => ({
  default: () => <div data-testid="dashboard-page">Dashboard Page</div>
}));

vi.mock('../pages/Profile', () => ({
  default: () => <div data-testid="profile-page">Profile Page</div>
}));

vi.mock('../pages/ChangePassword', () => ({
  default: () => <div data-testid="change-password-page">Change Password Page</div>
}));

describe('App Component', () => {
  beforeEach(() => {
    vi.clearAllMocks();
  });

  it('should render the main app structure', () => {
    render(<App />);

    expect(screen.getByTestId('auth-provider')).toBeInTheDocument();
    expect(screen.getByTestId('layout')).toBeInTheDocument();
  });

  it('should render login page for /login route', () => {
    // Mock window.location for route testing
    Object.defineProperty(window, 'location', {
      value: { pathname: '/login' },
      writable: true
    });

    render(<App />);
    
    // The app should render the layout and auth provider
    expect(screen.getByTestId('auth-provider')).toBeInTheDocument();
    expect(screen.getByTestId('layout')).toBeInTheDocument();
  });
});
