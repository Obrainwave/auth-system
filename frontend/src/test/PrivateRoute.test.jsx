import { render, screen } from '@testing-library/react';
import { describe, it, expect, vi, beforeEach } from 'vitest';
import { BrowserRouter } from 'react-router-dom';
import PrivateRoute from '../components/PrivateRoute';
import { useAuth } from '../contexts/AuthContext';

// Mock the auth context
vi.mock('../contexts/AuthContext', () => ({
  useAuth: vi.fn()
}));

const MockedPrivateRoute = ({ children, requireEmailVerification = false }) => (
  <BrowserRouter>
    <PrivateRoute requireEmailVerification={requireEmailVerification}>
      {children}
    </PrivateRoute>
  </BrowserRouter>
);

describe('PrivateRoute Component', () => {
  beforeEach(() => {
    vi.clearAllMocks();
  });

  it('should render children when user is authenticated', () => {
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

    render(
      <MockedPrivateRoute>
        <div data-testid="protected-content">Protected Content</div>
      </MockedPrivateRoute>
    );

    expect(screen.getByTestId('protected-content')).toBeInTheDocument();
  });

  it('should show loading spinner when loading', () => {
    useAuth.mockReturnValue({
      user: null,
      isAuthenticated: false,
      loading: true
    });

    render(
      <MockedPrivateRoute>
        <div data-testid="protected-content">Protected Content</div>
      </MockedPrivateRoute>
    );

    expect(screen.getByRole('status')).toBeInTheDocument();
    expect(screen.getByText('Loading...')).toBeInTheDocument();
    expect(screen.queryByTestId('protected-content')).not.toBeInTheDocument();
  });

  it('should redirect to login when not authenticated', () => {
    useAuth.mockReturnValue({
      user: null,
      isAuthenticated: false,
      loading: false
    });

    render(
      <MockedPrivateRoute>
        <div data-testid="protected-content">Protected Content</div>
      </MockedPrivateRoute>
    );

    expect(screen.queryByTestId('protected-content')).not.toBeInTheDocument();
  });

  it('should render children when email verification is not required', () => {
    const mockUser = {
      id: 1,
      name: 'John Doe',
      email: 'john@example.com',
      email_verified_at: null // Unverified email
    };

    useAuth.mockReturnValue({
      user: mockUser,
      isAuthenticated: true,
      loading: false
    });

    render(
      <MockedPrivateRoute requireEmailVerification={false}>
        <div data-testid="protected-content">Protected Content</div>
      </MockedPrivateRoute>
    );

    expect(screen.getByTestId('protected-content')).toBeInTheDocument();
  });

  it('should render children when email is verified and verification is required', () => {
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

    render(
      <MockedPrivateRoute requireEmailVerification={true}>
        <div data-testid="protected-content">Protected Content</div>
      </MockedPrivateRoute>
    );

    expect(screen.getByTestId('protected-content')).toBeInTheDocument();
  });

  it('should redirect when email verification is required but email is not verified', () => {
    const mockUser = {
      id: 1,
      name: 'John Doe',
      email: 'john@example.com',
      email_verified_at: null // Unverified email
    };

    useAuth.mockReturnValue({
      user: mockUser,
      isAuthenticated: true,
      loading: false
    });

    render(
      <MockedPrivateRoute requireEmailVerification={true}>
        <div data-testid="protected-content">Protected Content</div>
      </MockedPrivateRoute>
    );

    expect(screen.queryByTestId('protected-content')).not.toBeInTheDocument();
  });
});
