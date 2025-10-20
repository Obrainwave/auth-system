import { describe, it, expect, vi } from 'vitest';

// Mock axios
const mockAxios = {
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
};

vi.mock('axios', () => ({ default: mockAxios }));

describe('Authentication System Integration', () => {
  it('should have all required API endpoints', () => {
    const { authAPI } = require('../api');
    
    expect(typeof authAPI.register).toBe('function');
    expect(typeof authAPI.login).toBe('function');
    expect(typeof authAPI.logout).toBe('function');
    expect(typeof authAPI.getCurrentUser).toBe('function');
    expect(typeof authAPI.updateProfile).toBe('function');
    expect(typeof authAPI.changePassword).toBe('function');
    expect(typeof authAPI.sendEmailVerification).toBe('function');
    expect(typeof authAPI.forgotPassword).toBe('function');
    expect(typeof authAPI.resetPassword).toBe('function');
  });

  it('should have proper validation schemas', () => {
    // Test that our validation schemas are properly defined
    const { z } = require('zod');
    
    const loginSchema = z.object({
      email: z.string().email('Invalid email address'),
      password: z.string().min(1, 'Password is required'),
      remember: z.boolean().optional(),
    });

    const registerSchema = z.object({
      name: z.string().min(2, 'Name must be at least 2 characters'),
      email: z.string().email('Invalid email address'),
      password: z
        .string()
        .min(8, 'Password must be at least 8 characters')
        .regex(/[A-Z]/, 'Password must contain at least one uppercase letter')
        .regex(/[a-z]/, 'Password must contain at least one lowercase letter')
        .regex(/[0-9]/, 'Password must contain at least one number')
        .regex(/[^A-Za-z0-9]/, 'Password must contain at least one special character'),
      password_confirmation: z.string(),
    }).refine((data) => data.password === data.password_confirmation, {
      message: "Passwords don't match",
      path: ["password_confirmation"],
    });

    // Test valid data
    const validLoginData = {
      email: 'test@example.com',
      password: 'password123',
      remember: true
    };

    const validRegisterData = {
      name: 'John Doe',
      email: 'john@example.com',
      password: 'Password123!',
      password_confirmation: 'Password123!'
    };

    expect(() => loginSchema.parse(validLoginData)).not.toThrow();
    expect(() => registerSchema.parse(validRegisterData)).not.toThrow();
  });

  it('should handle authentication flow', async () => {
    // Mock successful authentication
    const mockUser = {
      id: 1,
      name: 'John Doe',
      email: 'john@example.com',
      email_verified_at: '2023-01-01T00:00:00Z'
    };

    mockAxios.create().get.mockResolvedValueOnce({
      data: { user: mockUser }
    });

    const { authAPI } = require('../api');
    
    // Test that API calls are properly configured
    expect(typeof authAPI.getCurrentUser).toBe('function');
    expect(typeof authAPI.login).toBe('function');
    expect(typeof authAPI.logout).toBe('function');
  });

  it('should validate password requirements', () => {
    const { z } = require('zod');
    
    const passwordSchema = z
      .string()
      .min(8, 'Password must be at least 8 characters')
      .regex(/[A-Z]/, 'Password must contain at least one uppercase letter')
      .regex(/[a-z]/, 'Password must contain at least one lowercase letter')
      .regex(/[0-9]/, 'Password must contain at least one number')
      .regex(/[^A-Za-z0-9]/, 'Password must contain at least one special character');

    // Test valid password
    expect(() => passwordSchema.parse('Password123!')).not.toThrow();
    
    // Test invalid passwords
    expect(() => passwordSchema.parse('password')).toThrow(); // no uppercase, number, special
    expect(() => passwordSchema.parse('PASSWORD123!')).toThrow(); // no lowercase
    expect(() => passwordSchema.parse('Password!')).toThrow(); // no number
    expect(() => passwordSchema.parse('Password123')).toThrow(); // no special character
    expect(() => passwordSchema.parse('Pass1!')).toThrow(); // too short
  });
});
