import { describe, it, expect, vi, beforeEach } from 'vitest';
import axios from 'axios';
import { authAPI } from '../api';

// Mock axios
vi.mock('axios', () => ({
  default: {
    create: vi.fn(() => ({
      interceptors: {
        request: { use: vi.fn() },
        response: { use: vi.fn() }
      },
      get: vi.fn(),
      post: vi.fn(),
      put: vi.fn(),
      delete: vi.fn()
    })),
    get: vi.fn()
  }
}));

describe('API Functions', () => {
  let mockApi;

  beforeEach(() => {
    vi.clearAllMocks();
    mockApi = {
      get: vi.fn(),
      post: vi.fn(),
      put: vi.fn(),
      delete: vi.fn()
    };
    
    // Mock the axios.create to return our mock API
    axios.default.create.mockReturnValue(mockApi);
  });

  describe('authAPI.register', () => {
    it('should call POST /register with user data', async () => {
      const userData = {
        name: 'John Doe',
        email: 'john@example.com',
        password: 'password123',
        password_confirmation: 'password123'
      };

      const mockResponse = {
        data: { message: 'Registration successful' }
      };

      mockApi.post.mockResolvedValueOnce(mockResponse);

      const result = await authAPI.register(userData);

      expect(mockApi.post).toHaveBeenCalledWith('/register', userData);
      expect(result).toEqual(mockResponse);
    });
  });

  describe('authAPI.login', () => {
    it('should call POST /login with credentials', async () => {
      const credentials = {
        email: 'john@example.com',
        password: 'password123',
        remember: false
      };

      const mockResponse = {
        data: { 
          user: { id: 1, name: 'John Doe', email: 'john@example.com' },
          token: 'mock-token'
        }
      };

      mockApi.post.mockResolvedValueOnce(mockResponse);

      const result = await authAPI.login(credentials);

      expect(mockApi.post).toHaveBeenCalledWith('/login', credentials);
      expect(result).toEqual(mockResponse);
    });
  });

  describe('authAPI.logout', () => {
    it('should call POST /logout', async () => {
      const mockResponse = {
        data: { message: 'Logged out successfully' }
      };

      mockApi.post.mockResolvedValueOnce(mockResponse);

      const result = await authAPI.logout();

      expect(mockApi.post).toHaveBeenCalledWith('/logout');
      expect(result).toEqual(mockResponse);
    });
  });

  describe('authAPI.getCurrentUser', () => {
    it('should call GET /user', async () => {
      const mockResponse = {
        data: { 
          user: { id: 1, name: 'John Doe', email: 'john@example.com' }
        }
      };

      mockApi.get.mockResolvedValueOnce(mockResponse);

      const result = await authAPI.getCurrentUser();

      expect(mockApi.get).toHaveBeenCalledWith('/user');
      expect(result).toEqual(mockResponse);
    });
  });

  describe('authAPI.updateProfile', () => {
    it('should call PUT /user/profile with profile data', async () => {
      const profileData = {
        name: 'John Updated',
        email: 'john.updated@example.com'
      };

      const mockResponse = {
        data: { 
          user: { id: 1, name: 'John Updated', email: 'john.updated@example.com' }
        }
      };

      mockApi.put.mockResolvedValueOnce(mockResponse);

      const result = await authAPI.updateProfile(profileData);

      expect(mockApi.put).toHaveBeenCalledWith('/user/profile', profileData);
      expect(result).toEqual(mockResponse);
    });
  });

  describe('authAPI.changePassword', () => {
    it('should call PUT /user/password with password data', async () => {
      const passwordData = {
        current_password: 'oldpassword',
        password: 'newpassword',
        password_confirmation: 'newpassword'
      };

      const mockResponse = {
        data: { message: 'Password changed successfully' }
      };

      mockApi.put.mockResolvedValueOnce(mockResponse);

      const result = await authAPI.changePassword(passwordData);

      expect(mockApi.put).toHaveBeenCalledWith('/user/password', passwordData);
      expect(result).toEqual(mockResponse);
    });
  });

  describe('authAPI.sendEmailVerification', () => {
    it('should call POST /email/verification-notification', async () => {
      const mockResponse = {
        data: { message: 'Verification email sent' }
      };

      mockApi.post.mockResolvedValueOnce(mockResponse);

      const result = await authAPI.sendEmailVerification();

      expect(mockApi.post).toHaveBeenCalledWith('/email/verification-notification');
      expect(result).toEqual(mockResponse);
    });
  });

  describe('authAPI.forgotPassword', () => {
    it('should call POST /forgot-password with email', async () => {
      const email = 'john@example.com';

      const mockResponse = {
        data: { message: 'Password reset email sent' }
      };

      mockApi.post.mockResolvedValueOnce(mockResponse);

      const result = await authAPI.forgotPassword(email);

      expect(mockApi.post).toHaveBeenCalledWith('/forgot-password', { email });
      expect(result).toEqual(mockResponse);
    });
  });

  describe('authAPI.resetPassword', () => {
    it('should call POST /reset-password with reset data', async () => {
      const resetData = {
        token: 'reset-token',
        email: 'john@example.com',
        password: 'newpassword',
        password_confirmation: 'newpassword'
      };

      const mockResponse = {
        data: { message: 'Password reset successfully' }
      };

      mockApi.post.mockResolvedValueOnce(mockResponse);

      const result = await authAPI.resetPassword(resetData);

      expect(mockApi.post).toHaveBeenCalledWith('/reset-password', resetData);
      expect(result).toEqual(mockResponse);
    });
  });
});
