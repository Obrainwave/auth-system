import { render, screen, waitFor } from '@testing-library/react';
import { describe, it, expect, vi, beforeEach } from 'vitest';
import { AuthProvider, useAuth } from '../contexts/AuthContext';
import { authAPI } from '../api';

// Mock the API
vi.mock('../api', () => ({
  authAPI: {
    getCurrentUser: vi.fn(),
    login: vi.fn(),
    register: vi.fn(),
    logout: vi.fn(),
    updateProfile: vi.fn(),
    changePassword: vi.fn(),
    sendEmailVerification: vi.fn(),
    forgotPassword: vi.fn(),
    resetPassword: vi.fn()
  }
}));

// Test component that uses the auth context
const TestComponent = () => {
  const { user, loading, isAuthenticated, login, register, logout } = useAuth();
  
  return (
    <div>
      <div data-testid="loading">{loading ? 'loading' : 'not loading'}</div>
      <div data-testid="authenticated">{isAuthenticated ? 'authenticated' : 'not authenticated'}</div>
      <div data-testid="user">{user ? user.name : 'no user'}</div>
      <button onClick={() => login({ email: 'test@test.com', password: 'password' })}>
        Login
      </button>
      <button onClick={() => register({ name: 'Test', email: 'test@test.com', password: 'password' })}>
        Register
      </button>
      <button onClick={logout}>
        Logout
      </button>
    </div>
  );
};

describe('AuthContext', () => {
  beforeEach(() => {
    vi.clearAllMocks();
  });

  it('should provide initial state', () => {
    render(
      <AuthProvider>
        <TestComponent />
      </AuthProvider>
    );

    expect(screen.getByTestId('loading')).toHaveTextContent('loading');
    expect(screen.getByTestId('authenticated')).toHaveTextContent('not authenticated');
    expect(screen.getByTestId('user')).toHaveTextContent('no user');
  });

  it('should handle successful authentication check', async () => {
    const mockUser = { id: 1, name: 'Test User', email: 'test@test.com' };
    authAPI.getCurrentUser.mockResolvedValueOnce({
      data: { user: mockUser }
    });

    render(
      <AuthProvider>
        <TestComponent />
      </AuthProvider>
    );

    await waitFor(() => {
      expect(screen.getByTestId('loading')).toHaveTextContent('not loading');
      expect(screen.getByTestId('authenticated')).toHaveTextContent('authenticated');
      expect(screen.getByTestId('user')).toHaveTextContent('Test User');
    });
  });

  it('should handle failed authentication check', async () => {
    authAPI.getCurrentUser.mockRejectedValueOnce(new Error('Unauthorized'));

    render(
      <AuthProvider>
        <TestComponent />
      </AuthProvider>
    );

    await waitFor(() => {
      expect(screen.getByTestId('loading')).toHaveTextContent('not loading');
      expect(screen.getByTestId('authenticated')).toHaveTextContent('not authenticated');
      expect(screen.getByTestId('user')).toHaveTextContent('no user');
    });
  });

  it('should handle successful login', async () => {
    const mockUser = { id: 1, name: 'Test User', email: 'test@test.com' };
    authAPI.getCurrentUser.mockRejectedValueOnce(new Error('Unauthorized'));
    authAPI.login.mockResolvedValueOnce({
      data: { user: mockUser }
    });

    render(
      <AuthProvider>
        <TestComponent />
      </AuthProvider>
    );

    await waitFor(() => {
      expect(screen.getByTestId('loading')).toHaveTextContent('not loading');
    });

    // Simulate login
    screen.getByText('Login').click();

    await waitFor(() => {
      expect(authAPI.login).toHaveBeenCalledWith({
        email: 'test@test.com',
        password: 'password'
      });
    });
  });

  it('should handle successful registration', async () => {
    authAPI.getCurrentUser.mockRejectedValueOnce(new Error('Unauthorized'));
    authAPI.register.mockResolvedValueOnce({
      data: { message: 'Registration successful' }
    });

    render(
      <AuthProvider>
        <TestComponent />
      </AuthProvider>
    );

    await waitFor(() => {
      expect(screen.getByTestId('loading')).toHaveTextContent('not loading');
    });

    // Simulate registration
    screen.getByText('Register').click();

    await waitFor(() => {
      expect(authAPI.register).toHaveBeenCalledWith({
        name: 'Test',
        email: 'test@test.com',
        password: 'password'
      });
    });
  });

  it('should handle logout', async () => {
    const mockUser = { id: 1, name: 'Test User', email: 'test@test.com' };
    authAPI.getCurrentUser.mockResolvedValueOnce({
      data: { user: mockUser }
    });
    authAPI.logout.mockResolvedValueOnce({});

    render(
      <AuthProvider>
        <TestComponent />
      </AuthProvider>
    );

    await waitFor(() => {
      expect(screen.getByTestId('authenticated')).toHaveTextContent('authenticated');
    });

    // Simulate logout
    screen.getByText('Logout').click();

    await waitFor(() => {
      expect(authAPI.logout).toHaveBeenCalled();
    });
  });
});
