jQuery(document).ready(function ($) {
    class CapExplorer {

        constructor() {
            this.blogSearch = $('[name="blog_search"]')
            this.userSearch = $('[name="user_search"]')
            this.blogsSelect = $('#blogs');
            this.rolesSelect = $('#roles');
            this.usersSelect = $('#users');
            this.query = $('#query')
            this.capabilities = $('#capabilities')
            this.loading = $('#loading');
            this.submit = $('#submit');
            this.clear = $('#clear');
            // Don't load if not on CapExplorer admin page
            if (! $('.toplevel_page_cap-explorer').is('*')) {
                return;
            }

            this.addListeners();
        }

        doSearch(endpoint, search, select) {
            let self = this;

            this.loading.show();
            select.hide().empty();

            wp.ajax.post(endpoint, {
                search: search,
            }).done(function (data) {
                let results = data.results;
                let count = data.count;

                self.loading.hide();
                if (results) {
                    select.append(results)
                        .attr('size', count)
                        .show();
                }
            });
        }

        addListeners() {
            let self = this;

            this.blogSearch.on('keyup', function () {
                let search = $(this).val();

                if (search.length < 3) {
                    return;
                }
                self.doSearch('search_blogs', search, self.blogsSelect);
            });

            this.blogsSelect.on('click', function () {
                let text = $(this).find('option:selected').text();

                self.blogSearch.val(text.replace(/\([\d]+\)/, ''));
            });

            this.userSearch.on('keyup', function () {
                let search = $(this).val();

                if (search.length < 3) {
                    return;
                }
                self.doSearch('search_users', search, self.usersSelect);
            });

            this.usersSelect.on('click', function () {
                let text = $(this).find('option:selected').text();

                self.userSearch.val(text.replace(/\([\w@.]+\)/, ''));
            });


            this.submit.on('click', function (event) {
                event.preventDefault();
                let blogId = self.blogsSelect.val();
                let role = self.rolesSelect.val();
                let user = self.usersSelect.val();
                let blogName = self.blogsSelect.find('option:selected').text();
                let roleName = self.rolesSelect.find('option:selected').text();
                let userName = self.usersSelect.find('option:selected').text();

                let empty = (blogId === '' || role === '' || user === '');
                if (empty) {
                    alert('Please select as Subsite, a Role, and a User');
                    return;
                }

                self.clear.trigger('click');
                self.loading.show();

                wp.ajax.post("show_caps", {
                    blog_id: blogId,
                    role: role,
                }).done(function (data) {
                    let label = 'Showing capabilities';
                    label += ' for user <strong>' + userName + '</strong>';
                    label += ' with role of <strong>' + roleName + '</strong>';
                    label += ' on <strong>' + blogName.replace(/\([\d]+\)/, '') + '</strong>';

                    self.loading.hide();
                    self.query.html(label);
                    self.capabilities.html(data.results);
                });
            });

            this.clear.on('click', function () {
                self.loading.hide();
                self.blogsSelect.hide();
                self.usersSelect.hide();
                self.query.empty();
                self.capabilities.empty();
            });
        }
    }

    new CapExplorer();
});
