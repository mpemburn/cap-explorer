<?php

namespace CapExplorer;

use WP_Roles;

class Explore
{
    protected int $blogCount = 0;
    protected int $userCount = 0;

    public function getUserCount(): int
    {
        return $this->userCount;
    }

   public function getBlogCount(): int
    {
        return $this->blogCount;
    }

    public function listBlogs(?string $search = null): array
    {
        global $wpdb;
        $blogList = [];

        $filter = $search ? "AND path LIKE '%{$search}%'" : '';
        $query = "
            SELECT blog_id, domain, path
            FROM {$wpdb->blogs}
            WHERE archived = 0 AND deleted = 0
            {$filter}
            ORDER BY blog_id";

        $blogs = $wpdb->get_results($query);
        foreach ($blogs as $blog) {
            $blogId = $blog->blog_id;

            $details = get_blog_details($blog->blog_id);

            if (! $details->blogname) {
                continue;
            }

            $blogList[$blogId] = $details->blogname;
        }

        $blogList = array_unique($blogList);

        $this->blogCount = count($blogList);

        return $blogList;
    }

    public function buildBlogsDropdown()
    {
        $html = '<span id="blog_search">';
        $html .= '<input type="text" name="blog_search" placeholder="Search for Subsites"/>';
        $html .= '<select id="blogs"></select>';
        $html .= '<span>';

        return $html;
    }

    public function buildBlogOptions(?string $search = null): string
    {
        $html = '';
        $blogs = $this->listBlogs($search);

        foreach ($blogs as $blogId => $name) {
            $html .= '<option value="' . $blogId . '">(' . $blogId . ') ' . $name . '</option>';
        }

        return $html;
    }

    public function listRoles(int $blogId = null): array
    {
        global $wp_roles;

        if (! isset($wp_roles)) {
            $wp_roles = new WP_Roles($blogId);
        }

        return $wp_roles->roles;
    }

    public function buildRolesDropdown(): string
    {
        $html = '';
        $roles = $this->listRoles();

        $html .= '<select id="roles">';
        $html .= '<option value="">Select Role</option>';
        foreach ($roles as $role => $name) {
            $html .= '<option value="' . $role . '">' . $name['name'] . '</option>';
        }
        $html .= '</select>';

        return $html;
    }

    public function listCapabilities(int $blogId, string $roleName): array
    {
        $roles = $this->listRoles($blogId);

        foreach ($roles as $role => $name) {
            if ($role === $roleName) {
                $caps = $name['capabilities'];
                ksort($caps);
                return $caps;
            }
        }

        return [];
    }

    public function buildCapabilitiesTable(int $blogId, string $roleName): string
    {
        $html = '';
        $capabilities = $this->listCapabilities($blogId, $roleName);
        $chunks = array_chunk($capabilities, 4, true);

        $html .= '<table id="cap_table">';
        $html .= '<tr class="top-row">';
        $html .= '<td colspan="4">';
        $html .= 'Total capabilities: ' . count($capabilities);
        $html .= '</td>';
        $html .= '</tr>';
        foreach ($chunks as $chunk) {
            $html .= '<tr>';
            foreach ($chunk as $capability => $can) {
                $html .= '<td>' . $capability . '</td>';
            }
            $html .= '</tr>';
        }
        $html .= '</table>';

        return $html;
    }

    public function listUsers(?string $search = null): array
    {
        $userList = [];
        $filter = $search ? ['search' => '*' . $search . '*'] : null;

        foreach (get_users($filter) as $user) {
            $thisUser = [];
            $thisUser['id'] = $user->get('ID');
            $thisUser['name'] = $user->get('display_name');
            $thisUser['email'] = $user->get('user_email');
            $userList[] = $thisUser;
        }

        $userList = array_unique($userList);

        $this->userCount = count($userList);

        return $userList;
    }

    public function buildUsersDropdown()
    {
        $html = '<span id="user_search">';
        $html .= '<input type="text" name="user_search" placeholder="Search for Users"/>';
        $html .= '<select id="users"></select>';
        $html .= '<span>';

        return $html;
    }

    public function buildUsersOptions(string $search): string
    {
        $html = '';

        $users = $this->listUsers($search);

        foreach ($users as $user) {
            $html .= '<option value="' . $user['id'] . '">' . $user['name'] . ' (' . $user['email'] . ')</option>';
        }

        return $html;
    }

    public function listUserCapabilities(int $userId): array
    {
        $roleCapabilities = [];
        $userData = get_userdata($userId);
        $userRoles = $userData->roles;

        foreach ($userRoles as $role) {
            $roleObject = get_role($role);
            $roleCapabilities = $roleObject->capabilities;
        }

        return $roleCapabilities;
    }
}
