import axios from 'axios';

// Create axios instance
const api = axios.create({
  baseURL: '/api',
  withCredentials: true,
  headers: {
    'Content-Type': 'application/json',
    'Accept': 'application/json',
  },
});

// Request interceptor to fetch CSRF cookie before state-changing requests
api.interceptors.request.use(
  async (config) => {
    // Only fetch CSRF cookie for state-changing requests
    if (['post', 'put', 'patch', 'delete'].includes(config.method.toLowerCase())) {
      try {
        await axios.get('/sanctum/csrf-cookie', {
          withCredentials: true,
        });
      } catch (error) {
        console.error('Failed to fetch CSRF cookie:', error);
      }
    }
    return config;
  },
  (error) => {
    return Promise.reject(error);
  }
);

// Response interceptor to handle auth errors
api.interceptors.response.use(
  (response) => response,
  (error) => {
    if (error.response?.status === 401 || error.response?.status === 419) {
      // Only redirect if not already on login page and not checking auth
      const currentPath = window.location.pathname;
      const isAuthCheck = error.config?.url?.includes('/user');
      
      if (currentPath !== '/login' && !isAuthCheck) {
        window.location.href = '/login';
      }
    }
    return Promise.reject(error);
  }
);

// Auth API methods
export const authAPI = {
  // Register user
  register: (userData) => api.post('/register', userData),
  
  // Login user
  login: (credentials) => api.post('/login', credentials),
  
  // Logout user
  logout: () => api.post('/logout'),
  
  // Get current user
  getCurrentUser: () => api.get('/user'),
  
  // Update user profile
  updateProfile: (profileData) => api.put('/user/profile', profileData),
  
  // Change password
  changePassword: (passwordData) => api.put('/user/password', passwordData),
  
  // Send email verification
  sendEmailVerification: () => api.post('/email/verification-notification'),
  
  // Forgot password
  forgotPassword: (email) => api.post('/forgot-password', { email }),
  
  // Reset password
  resetPassword: (resetData) => api.post('/reset-password', resetData),
};

export default api;
