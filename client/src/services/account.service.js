import http from './http.service'

async function login(username, password) {
  const response = await http.post("/account/login", {username, password})
  return await response.json();
}

function logout() {
  return http.get("/logout")
}

async function getUser() {
  const response = await http.get("/account/user");
  return await response.json();
}

export const accountService = {
  login,
  logout,
  getUser
};
