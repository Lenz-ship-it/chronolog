import { API_BASE } from '$lib/config';
import { user } from '$lib/stores/user';
import { goto } from '$app/navigation';
import { base } from '$app/paths';

function getToken() {
  return localStorage.getItem('token');
}

async function request(endpoint, options = {}) {
  const token = localStorage.getItem('token');
  const headers = {
    'Content-Type': 'application/json',
    ...(options.headers || {})
  };
  if (token) headers['Authorization'] = `Bearer ${token}`;

  const res = await fetch(`${API_BASE}/${endpoint}`, {
    ...options,
    headers
  });

  let data = {};
  try {
    data = await res.json();
  } catch (err) {
    data = {};
  }

  if (res.status === 401) {
    if (
      data?.error === "Invalid or expired token" ||
      data?.error === "No token provided"
    ) {
      user.set(null);
      localStorage.removeItem("user");
      localStorage.removeItem("token");

      goto(`${base}/login`, { replaceState: true });

      throw new Error("Session expired. Please log in again");
    }

    throw new Error(data.error || "Unauthorized");
  }


  if (!res.ok) {
    throw new Error(data.error || `Request failed: ${res.status}`);
  }

  return data;
}


/* ========== AUTH API ========== */
export const register = (payload) =>
  request('Auth.php?action=register', {
    method: 'POST',
    body: JSON.stringify(payload),
  });

export const login = async (payload) => {
  const data = await request('Auth.php?action=login', {
    method: 'POST',
    body: JSON.stringify(payload),
  });

  if (data?.token) {
    localStorage.setItem('token', data.token);
  }
  return data;
};

export const resetPassword = (payload) =>
  request('Auth.php?action=resetPassword', {
    method: 'POST',
    body: JSON.stringify(payload),
  });


/* ========== JOURNAL CRUD ========== */
export const createEntry = (payload) =>
  request('JournalCRUD.php?action=create', {
    method: 'POST',
    body: JSON.stringify(payload),
  });

export const readEntries = async (filters = {}) => {
  const params = new URLSearchParams(filters).toString();
  return request(`JournalCRUD.php?action=read${params ? `&${params}` : ''}`);
};

export const updateEntry = (payload) =>
  request('JournalCRUD.php?action=update', {
    method: 'POST',
    body: JSON.stringify(payload),
  });

export const deleteEntry = (payload) =>
  request('JournalCRUD.php?action=delete', {
    method: 'POST',
    body: JSON.stringify(payload),
  });

/* ========== QUOTES ========== */
export const getRandomQuote = async () =>
  request('JournalCRUD.php?action=quote');
