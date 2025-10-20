import React, { useState } from 'react';
import { useForm } from 'react-hook-form';
import { zodResolver } from '@hookform/resolvers/zod';
import { z } from 'zod';
import { useAuth } from '../contexts/AuthContext';
import PrivateRoute from '../components/PrivateRoute';

const profileSchema = z.object({
  name: z.string().min(2, 'Name must be at least 2 characters'),
  email: z.string().email('Invalid email address'),
});

const Profile = () => {
  const [isLoading, setIsLoading] = useState(false);
  const [error, setError] = useState('');
  const [success, setSuccess] = useState('');
  const { user, updateProfile } = useAuth();

  const {
    register,
    handleSubmit,
    formState: { errors },
  } = useForm({
    resolver: zodResolver(profileSchema),
    defaultValues: {
      name: user?.name || '',
      email: user?.email || '',
    },
  });

  const onSubmit = async (data) => {
    setIsLoading(true);
    setError('');
    setSuccess('');

    const result = await updateProfile(data);

    if (result.success) {
      setSuccess('Profile updated successfully!');
      if (data.email !== user?.email) {
        setSuccess('Profile updated successfully! Please check your email for verification.');
      }
    } else {
      setError(result.error);
    }

    setIsLoading(false);
  };

  return (
    <PrivateRoute>
      <div className="container mt-4">
        <div className="row justify-content-center">
          <div className="col-md-8 col-lg-6">
            <div className="card shadow">
              <div className="card-body p-4">
                <h1 className="card-title h2 mb-4">
                  Profile Settings
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
                    <label htmlFor="name" className="form-label">
                      Full Name
                    </label>
                    <input
                      {...register('name')}
                      type="text"
                      className={`form-control ${errors.name ? 'is-invalid' : ''}`}
                    />
                    {errors.name && (
                      <div className="invalid-feedback">{errors.name.message}</div>
                    )}
                  </div>
                  
                  <div className="mb-3">
                    <label htmlFor="email" className="form-label">
                      Email Address
                    </label>
                    <input
                      {...register('email')}
                      type="email"
                      className={`form-control ${errors.email ? 'is-invalid' : ''}`}
                    />
                    {errors.email && (
                      <div className="invalid-feedback">{errors.email.message}</div>
                    )}
                    <div className="form-text">
                      If you change your email, you'll need to verify the new address.
                    </div>
                  </div>
                  
                  <div className="mb-4">
                    <div className="alert alert-info">
                      <strong>Email Status:</strong> {user?.email_verified_at ? 'Verified' : 'Unverified'}
                      {!user?.email_verified_at && (
                        <div className="mt-2">
                          <small className="text-warning">
                            Please verify your email address to access all features.
                          </small>
                        </div>
                      )}
                    </div>
                  </div>
                  
                  <div className="d-flex justify-content-end">
                    <button
                      type="submit"
                      className="btn btn-primary"
                      disabled={isLoading}
                    >
                      {isLoading ? (
                        <>
                          <span className="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>
                          Updating...
                        </>
                      ) : (
                        'Update Profile'
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

export default Profile;
