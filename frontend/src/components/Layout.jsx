import React from 'react';
import { Link, useNavigate } from 'react-router-dom';
import { useAuth } from '../contexts/AuthContext';

const Layout = ({ children }) => {
  const { user, logout, isAuthenticated } = useAuth();
  const navigate = useNavigate();

  const handleLogout = async () => {
    await logout();
    navigate('/login');
  };

  return (
    <div className="min-vh-100 bg-light">
      <nav className="navbar navbar-expand-lg navbar-light bg-white shadow-sm border-bottom">
        <div className="container">
          <Link to="/" className="navbar-brand fw-bold">
            Auth System
          </Link>
          
          <div className="navbar-nav ms-auto">
            {isAuthenticated ? (
              <div className="d-flex align-items-center gap-3">
                <span className="text-muted">
                  Welcome, {user?.name}
                </span>
                <Link to="/profile" className="nav-link">
                  Profile
                </Link>
                <button
                  onClick={handleLogout}
                  className="btn btn-outline-danger btn-sm"
                >
                  Logout
                </button>
              </div>
            ) : (
              <div className="d-flex align-items-center gap-2">
                <Link to="/login" className="nav-link">
                  Login
                </Link>
                <Link to="/register" className="btn btn-primary btn-sm">
                  Register
                </Link>
              </div>
            )}
          </div>
        </div>
      </nav>
      
      <main className="container-fluid py-4">
        {children}
      </main>
    </div>
  );
};

export default Layout;
