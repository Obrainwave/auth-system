import '@testing-library/jest-dom';
import { vi } from 'vitest';

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

// Mock react-router-dom
vi.mock('react-router-dom', async () => {
  const actual = await vi.importActual('react-router-dom');
  return {
    ...actual,
    useNavigate: () => vi.fn(),
    useLocation: () => ({ pathname: '/', state: null })
  };
});

// Mock window.location
Object.defineProperty(window, 'location', {
  value: {
    pathname: '/',
    href: '/',
    assign: vi.fn(),
    replace: vi.fn()
  },
  writable: true
});
