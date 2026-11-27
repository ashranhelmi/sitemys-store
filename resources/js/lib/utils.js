import { clsx } from "clsx";
import { twMerge } from "tailwind-merge"

export function cn(...inputs) {
  return twMerge(clsx(inputs));
}

// Public file URL that also works when the app runs in a subfolder (APP_URL comes through Ziggy's @routes).
export function asset(path) {
  // route("home") is the app root (APP_URL), subfolder included. Ziggy's @routes data is not on window.
  const base = typeof route === "function" ? route("home") : "";
  return `${base.replace(/\/$/, "")}/${String(path).replace(/^\//, "")}`;
}
