import { render, screen } from '@testing-library/react';
import { describe, it, expect, vi, beforeEach } from 'vitest';
import { BrowserRouter } from 'react-router-dom';
import Dashboard from '../pages/Dashboard';
import { useAuth } from '../contexts/AuthContext';

// Mock the auth context
vi.mock('../contexts/AuthContext', () => ({
  useAuth: vi.fn()
}));

// Mock PrivateRoute component
vi.mock('../components/PrivateRoute', () => ({
  default: ({ children }) => <div data-testid="private-route">{children}</div>
}));

const MockedDashboard = () => (
  <BrowserRouter>
    <Dashboard />
  </BrowserRouter>
);

describe('Dashboard Component', () => {
  beforeEach(() => {
    vi.clearAllMocks();
  });

  it('should render dashboard with user name', () => {
    const mockUser = {
      id: 1,
      name: 'John Doe',
      email: 'john@example.com',
      email_verified_at: '2023-01-01T00:00:00Z'
    };

    useAuth.mockReturnValue({
      user: mockUser,
      isAuthenticated: true,
      loading: false
    });

    render(<MockedDashboard />);

    expect(screen.getByText('Welcome to your Dashboard, John Doe!')).toBeInTheDocument();
  });

  it('should render profile management card', () => {
    const mockUser = {
      id: 1,
      name: 'John Doe',
      email: 'john@example.com',
      email_verified_at: '2023-01-01T00:00:00Z'
    };

    useAuth.mockReturnValue({
      user: mockUser,
      isAuthenticated: true,
      loading: false
    });

    render(<MockedDashboard />);

    expect(screen.getByText('Profile Management')).toBeInTheDocument();
    expect(screen.getByText('Update your personal information and account settings.')).toBeInTheDocument();
    
    const profileLink = screen.getByText('Manage Profile');
    expect(profileLink).toBeInTheDocument();
    expect(profileLink.closest('a')).toHaveAttribute('href', '/profile');
  });

  it('should render security card', () => {
    const mockUser = {
      id: 1,
      name: 'John Doe',
      email: 'john@example.com',
      email_verified_at: '2023-01-01T00:00:00Z'
    };

    useAuth.mockReturnValue({
      user: mockUser,
      isAuthenticated: true,
      loading: false
    });

    render(<MockedDashboard />);

    expect(screen.getByText('Security')).toBeInTheDocument();
    expect(screen.getByText('Change your password and manage security settings.')).toBeInTheDocument();
    
    const securityLink = screen.getByText('Security Settings');
    expect(securityLink).toBeInTheDocument();
    expect(securityLink.closest('a')).toHaveAttribute('href', '/change-password');
  });

  it('should render account status card with verified email', () => {
    const mockUser = {
      id: 1,
      name: 'John Doe',
      email: 'john@example.com',
      email_verified_at: '2023-01-01T00:00:00Z'
    };

    useAuth.mockReturnValue({
      user: mockUser,
      isAuthenticated: true,
      loading: false
    });

    render(<MockedDashboard />);

    expect(screen.getByText('Account Status')).toBeInTheDocument();
    expect(screen.getByText('john@example.com')).toBeInTheDocument();
    expect(screen.getByText('Verified')).toBeInTheDocument();
  });

  it('should render account status card with unverified email', () => {
    const mockUser = {
      id: 1,
      name: 'John Doe',
      email: 'john@example.com',
      email_verified_at: null
    };

    useAuth.mockReturnValue({
      user: mockUser,
      isAuthenticated: true,
      loading: false
    });

    render(<MockedDashboard />);

    expect(screen.getByText('Account Status')).toBeInTheDocument();
    expect(screen.getByText('john@example.com')).toBeInTheDocument();
    expect(screen.getByText('Unverified')).toBeInTheDocument();
  });

  it('should handle missing user data gracefully', () => {
    useAuth.mockReturnValue({
      user: null,
      isAuthenticated: true,
      loading: false
    });

    render(<MockedDashboard />);

    expect(screen.getByText('Welcome to your Dashboard, !')).toBeInTheDocument();
  });
});
