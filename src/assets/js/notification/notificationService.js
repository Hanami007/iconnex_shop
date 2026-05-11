/**
 * Notification Service Module
 * Handles API calls and state for the notification system.
 */

const NotificationService = {
  async getUnreadCount() {
    try {
      const res = await fetch('api/notifications.php?action=unread_count');
      return await res.json();
    } catch (err) {
      console.error("Failed to fetch unread count:", err);
      return { success: false, error: err };
    }
  },

  async getNotifications() {
    try {
      const res = await fetch('api/notifications.php?action=list');
      return await res.json();
    } catch (err) {
      console.error("Failed to fetch notifications:", err);
      return { success: false, error: err };
    }
  },

  async markAsRead(id) {
    try {
      const fd = new FormData();
      fd.append('id', id);
      const res = await fetch('api/notifications.php?action=mark_read', { method: 'POST', body: fd });
      return await res.json();
    } catch (err) {
      console.error("Failed to mark notification as read:", err);
      return { success: false, error: err };
    }
  }
};

window.NotificationService = NotificationService;
