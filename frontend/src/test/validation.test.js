import { describe, it, expect } from 'vitest';
import { z } from 'zod';

// Import validation schemas from components
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

const profileSchema = z.object({
  name: z.string().min(2, 'Name must be at least 2 characters'),
  email: z.string().email('Invalid email address'),
});

const changePasswordSchema = z.object({
  current_password: z.string().min(1, 'Current password is required'),
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

describe('Validation Schemas', () => {
  describe('Login Schema', () => {
    it('should validate correct login data', () => {
      const validData = {
        email: 'test@example.com',
        password: 'password123',
        remember: true
      };

      expect(() => loginSchema.parse(validData)).not.toThrow();
    });

    it('should reject invalid email', () => {
      const invalidData = {
        email: 'invalid-email',
        password: 'password123'
      };

      expect(() => loginSchema.parse(invalidData)).toThrow('Invalid email address');
    });

    it('should reject empty password', () => {
      const invalidData = {
        email: 'test@example.com',
        password: ''
      };

      expect(() => loginSchema.parse(invalidData)).toThrow('Password is required');
    });

    it('should accept optional remember field', () => {
      const validData = {
        email: 'test@example.com',
        password: 'password123'
      };

      expect(() => loginSchema.parse(validData)).not.toThrow();
    });
  });

  describe('Register Schema', () => {
    it('should validate correct registration data', () => {
      const validData = {
        name: 'John Doe',
        email: 'john@example.com',
        password: 'Password123!',
        password_confirmation: 'Password123!'
      };

      expect(() => registerSchema.parse(validData)).not.toThrow();
    });

    it('should reject short name', () => {
      const invalidData = {
        name: 'J',
        email: 'john@example.com',
        password: 'Password123!',
        password_confirmation: 'Password123!'
      };

      expect(() => registerSchema.parse(invalidData)).toThrow('Name must be at least 2 characters');
    });

    it('should reject invalid email', () => {
      const invalidData = {
        name: 'John Doe',
        email: 'invalid-email',
        password: 'Password123!',
        password_confirmation: 'Password123!'
      };

      expect(() => registerSchema.parse(invalidData)).toThrow('Invalid email address');
    });

    it('should reject short password', () => {
      const invalidData = {
        name: 'John Doe',
        email: 'john@example.com',
        password: 'Pass1!',
        password_confirmation: 'Pass1!'
      };

      expect(() => registerSchema.parse(invalidData)).toThrow('Password must be at least 8 characters');
    });

    it('should reject password without uppercase', () => {
      const invalidData = {
        name: 'John Doe',
        email: 'john@example.com',
        password: 'password123!',
        password_confirmation: 'password123!'
      };

      expect(() => registerSchema.parse(invalidData)).toThrow('Password must contain at least one uppercase letter');
    });

    it('should reject password without lowercase', () => {
      const invalidData = {
        name: 'John Doe',
        email: 'john@example.com',
        password: 'PASSWORD123!',
        password_confirmation: 'PASSWORD123!'
      };

      expect(() => registerSchema.parse(invalidData)).toThrow('Password must contain at least one lowercase letter');
    });

    it('should reject password without number', () => {
      const invalidData = {
        name: 'John Doe',
        email: 'john@example.com',
        password: 'Password!',
        password_confirmation: 'Password!'
      };

      expect(() => registerSchema.parse(invalidData)).toThrow('Password must contain at least one number');
    });

    it('should reject password without special character', () => {
      const invalidData = {
        name: 'John Doe',
        email: 'john@example.com',
        password: 'Password123',
        password_confirmation: 'Password123'
      };

      expect(() => registerSchema.parse(invalidData)).toThrow('Password must contain at least one special character');
    });

    it('should reject mismatched passwords', () => {
      const invalidData = {
        name: 'John Doe',
        email: 'john@example.com',
        password: 'Password123!',
        password_confirmation: 'Different123!'
      };

      expect(() => registerSchema.parse(invalidData)).toThrow("Passwords don't match");
    });
  });

  describe('Profile Schema', () => {
    it('should validate correct profile data', () => {
      const validData = {
        name: 'John Doe',
        email: 'john@example.com'
      };

      expect(() => profileSchema.parse(validData)).not.toThrow();
    });

    it('should reject short name', () => {
      const invalidData = {
        name: 'J',
        email: 'john@example.com'
      };

      expect(() => profileSchema.parse(invalidData)).toThrow('Name must be at least 2 characters');
    });

    it('should reject invalid email', () => {
      const invalidData = {
        name: 'John Doe',
        email: 'invalid-email'
      };

      expect(() => profileSchema.parse(invalidData)).toThrow('Invalid email address');
    });
  });

  describe('Change Password Schema', () => {
    it('should validate correct password change data', () => {
      const validData = {
        current_password: 'OldPassword123!',
        password: 'NewPassword123!',
        password_confirmation: 'NewPassword123!'
      };

      expect(() => changePasswordSchema.parse(validData)).not.toThrow();
    });

    it('should reject empty current password', () => {
      const invalidData = {
        current_password: '',
        password: 'NewPassword123!',
        password_confirmation: 'NewPassword123!'
      };

      expect(() => changePasswordSchema.parse(invalidData)).toThrow('Current password is required');
    });

    it('should reject short new password', () => {
      const invalidData = {
        current_password: 'OldPassword123!',
        password: 'New1!',
        password_confirmation: 'New1!'
      };

      expect(() => changePasswordSchema.parse(invalidData)).toThrow('Password must be at least 8 characters');
    });

    it('should reject mismatched passwords', () => {
      const invalidData = {
        current_password: 'OldPassword123!',
        password: 'NewPassword123!',
        password_confirmation: 'DifferentPassword123!'
      };

      expect(() => changePasswordSchema.parse(invalidData)).toThrow("Passwords don't match");
    });
  });
});
