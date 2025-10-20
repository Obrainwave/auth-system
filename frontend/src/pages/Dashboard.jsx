import React from 'react';
import { Link } from 'react-router-dom';
import { useAuth } from '../contexts/AuthContext';
import PrivateRoute from '../components/PrivateRoute';

const Dashboard = () => {
  const { user } = useAuth();

  return (
    <PrivateRoute requireEmailVerification={true}>
      <div className="container mt-4">
        <div className="card shadow">
          <div className="card-body p-4">
            <h1 className="card-title h2 mb-4">
              Welcome to your Dashboard, {user?.name}!
            </h1>
            
            <div className="row g-4">
              <div className="col-md-6 col-lg-4">
                <div className="card bg-primary bg-opacity-10 border-primary">
                  <div className="card-body">
                    <h3 className="card-title h5 text-primary mb-3">
                      Profile Management
                    </h3>
                    <p className="card-text text-primary mb-3">
                      Update your personal information and account settings.
                    </p>
                    <Link to="/profile" className="btn btn-outline-primary">
                      Manage Profile
                    </Link>
                  </div>
                </div>
              </div>
              
              <div className="col-md-6 col-lg-4">
                <div className="card bg-success bg-opacity-10 border-success">
                  <div className="card-body">
                    <h3 className="card-title h5 text-success mb-3">
                      Security
                    </h3>
                    <p className="card-text text-success mb-3">
                      Change your password and manage security settings.
                    </p>
                    <Link to="/change-password" className="btn btn-outline-success">
                      Security Settings
                    </Link>
                  </div>
                </div>
              </div>
              
              <div className="col-md-6 col-lg-4">
                <div className="card bg-info bg-opacity-10 border-info">
                  <div className="card-body">
                    <h3 className="card-title h5 text-info mb-3">
                      Account Status
                    </h3>
                    <p className="card-text text-info mb-2">
                      <strong>Email:</strong> {user?.email}
                    </p>
                    <p className="card-text text-info">
                      <strong>Status:</strong> {user?.email_verified_at ? 'Verified' : 'Unverified'}
                    </p>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </PrivateRoute>
  );
};

export default Dashboard;
