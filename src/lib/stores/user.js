import { writable } from 'svelte/store';

const isBrowser = typeof window !== 'undefined';
const savedUser = isBrowser && localStorage.getItem('user')
  ? JSON.parse(localStorage.getItem('user'))
  : null;

export const user = writable(savedUser);

if (isBrowser) {
  user.subscribe((value) => {
    if (value) localStorage.setItem('user', JSON.stringify(value));
    else localStorage.removeItem('user');
  });
}

export function logout() {
  user.set(null);
  if (isBrowser) {
    localStorage.removeItem('user');
    localStorage.removeItem('token');
  }
}
