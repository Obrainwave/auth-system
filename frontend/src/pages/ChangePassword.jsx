import React, { useState } from 'react';
import { useForm } from 'react-hook-form';
import { zodResolver } from '@hookform/resolvers/zod';
import { z } from 'zod';
import { useAuth } from '../contexts/AuthContext';
import PrivateRoute from '../components/PrivateRoute';

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

const ChangePassword = () => {
  const [isLoading, setIsLoading] = useState(false);
  const [error, setError] = useState('');
  const [success, setSuccess] = useState('');
  const { changePassword } = useAuth();

  const {
    register,
    handleSubmit,
    formState: { errors },
    reset,
  } = useForm({
    resolver: zodResolver(changePasswordSchema),
  });

  const onSubmit = async (data) => {
    setIsLoading(true);
    setError('');
    setSuccess('');

    const result = await changePassword(data);

    if (result.success) {
      setSuccess('Password changed successfully!');
      reset();
    } else {
      setError(result.error);
    }

    setIsLoading(false);
  };

  return (
    <PrivateRoute>
      <div className="container mt-4">
        <div className="row justify-content-center">
          <div className="col-md-6 col-lg-5">
            <div className="card shadow">
              <div className="card-body p-4">
                <h1 className="card-title h2 mb-4">
                  Change Password
                </h1>
                
                <form onSubmit={handleSubmit(onSubmit)}>
                  {error && (
                    <div className="alert alert-danger" role="alert">
                      {error}
                    </div>
                  )}
                  
                  {success && (
                    <div className="alert alert-success" role="alert">
                      {success}
                    </div>
                  )}
                  
                  <div className="mb-3">
                    <label htmlFor="current_password" className="form-label">
                      Current Password
                    </label>
                    <input
                      {...register('current_password')}
                      type="password"
                      className={`form-control ${errors.current_password ? 'is-invalid' : ''}`}
                      placeholder="Enter your current password"
                      autoComplete="current-password"
                    />
                    {errors.current_password && (
                      <div className="invalid-feedback">{errors.current_password.message}</div>
                    )}
                  </div>
                  
                  <div className="mb-3">
                    <label htmlFor="password" className="form-label">
                      New Password
                    </label>
                    <input
                      {...register('password')}
                      type="password"
                      className={`form-control ${errors.password ? 'is-invalid' : ''}`}
                      placeholder="Enter your new password"
                      autoComplete="new-password"
                    />
                    {errors.password && (
                      <div className="invalid-feedback">{errors.password.message}</div>
                    )}
                    <div className="form-text">
                      Password must be at least 8 characters with uppercase, lowercase, number, and special character.
                    </div>
                  </div>
                  
                  <div className="mb-4">
                    <label htmlFor="password_confirmation" className="form-label">
                      Confirm New Password
                    </label>
                    <input
                      {...register('password_confirmation')}
                      type="password"
                      className={`form-control ${errors.password_confirmation ? 'is-invalid' : ''}`}
                      placeholder="Confirm your new password"
                      autoComplete="new-password"
                    />
                    {errors.password_confirmation && (
                      <div className="invalid-feedback">{errors.password_confirmation.message}</div>
                    )}
                  </div>
                  
                  <div className="d-flex justify-content-end gap-2">
                    <button
                      type="button"
                      className="btn btn-outline-secondary"
                      onClick={() => reset()}
                    >
                      Reset
                    </button>
                    <button
                      type="submit"
                      className="btn btn-primary"
                      disabled={isLoading}
                    >
                      {isLoading ? (
                        <>
                          <span className="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>
                          Changing...
                        </>
                      ) : (
                        'Change Password'
                      )}
                    </button>
                  </div>
                </form>
              </div>
            </div>
          </div>
        </div>
      </div>
    </PrivateRoute>
  );
};

export default ChangePassword;
