/*globals $ptty*/

//based on the docs example
const cbf_login = {
    name: 'login',
    method: function (cmd) {
        let opts, $input = $ptty.get_terminal('.prompt .input');

        if (cmd[1] && cmd[2]) {
            opts = {
                out: 'Identifying...',
                last: 'xxxxxxxxxx',
                data: {usr: cmd[1], psw: $input.text()}
            };
            $input
                .text('')
                .css({'visibility': 'visible'});
        } else if (cmd[1]) {
            opts = {
                out: 'Password?',
                next: 'login ' + cmd[1] + ' %cmd%',
            };

            $input.css({'visibility': 'hidden'});
            $(document).on('keydown.escape', function (e) {
                if (e.which === 27) { // escape key exits command
                    $input.css({'visibility': 'visible'});
                    $(this).unbind(e);
                }
            });

            cmd = false;
        } else {
            opts = {
                out: 'Username:',
                ps: '> ',
                next: 'login %cmd%',
            };
            cmd = false;
        }

        $ptty.set_command_option(opts);

        return cmd;
    }
};
$ptty.register('callbefore', cbf_login);

const cmd_login = {
    name: 'login',
    method: function (cmd) {
        const xhr = $.ajax({
            url: "/index.php/login",
            method: "POST",
            data: {"username": cmd[1], "password": cmd[2]},
            type: "json",
            async: false
        });
        xhr.done(
            /**
             * @param {String} data.token
             * @param {String} data.user
             * @param {Boolean} data.isAdmin
             */
            function (data) {
                $.ajaxSetup({headers: {"X-AUTH-TOKEN": data.token}});
                cmd.data = {is_loggedin: true, isAdmin: data.isAdmin};
                cmd.ps = '~' + data.user + ' ' + (data.isAdmin ? '#' : '$') + '>';
                $ptty.change_settings({ps: cmd.ps});

            });
        xhr.fail(function (jqXHR, textStatus, errorThrown) {

        });
        return cmd;
    },
    options: [1, 2],
    help: 'Login command. Usage: login [username] [password]'
};
$ptty.register('command', cmd_login);

//region users
let cmd_users = {
    name: 'users',
    method: function (cmd) {
        let users = [];
        $.get({url: '/index.php/api/users', async: false}).done(function (res) {
            users = res;
        });
        cmd.out = users.map(function (v) {
            return v.name;
        }).join(' ');
        return cmd;
    },
    options: [],
    help: 'List existing users.'
};

let cmd_id = {
    name: 'id',
    help: 'Print user and group information about the specified USER, or (when the USER is omitted) for the current USER',
    method: function (cmd) {
        if (typeof cmd[1] === "undefined") {
            cmd[1] = 0;
        }
        $.get({url: '/index.php/api/users/' + cmd[1], async: false}).done(function (res) {
            cmd.out = 'uid=' + res.id + '(' + res.name + ') is_admin=' + (res.isAdmin ? 'Y' : 'N') + ' groups=';
            if (res.groups.lenght) {
                for (let grp of res.groups) {
                    cmd.out += '' + grp.id + '(' + grp.name + '),';
                }
                cmd.out = cmd.out.substring(0, cmd.out.length - 1);
            }
        }).fail(function (xhr) {
            if (xhr.status === 404) {
                cmd.out = 'User not found';
            }
        });
        return cmd;
    },
    options: [1]
};

const cbf_useradd = {
    name: 'useradd',
    method: function (cmd) {
        let opts, $input = $ptty.get_terminal('.prompt .input');

        if (cmd[1] && cmd[2] && cmd[3]) {
            opts = {
                out: 'Creating...',
                last: ' ',
                data: {usr: cmd[1], psw: cmd[2], adm: $input.text()}
            };
            $input
                .text('')
                .css({'visibility': 'visible'});
        } else if (cmd[1] && cmd[2]) {
            opts = {
                out: 'Is Admin (Y/N)?',
                ps: '[N]> ',
                next: 'useradd ' + cmd[1] + ' ' + cmd[2] + ' %cmd%',
            };
            $input.css({'visibility': 'visible'});

            cmd = false;
        } else if (cmd[1]) {
            opts = {
                out: 'Password?',
                next: 'useradd ' + cmd[1] + ' %cmd%',
            };

            $input.css({'visibility': 'hidden'});
            $(document).on('keydown.escape', function (e) {
                if (e.which === 27) { // escape key exits command
                    $input.css({'visibility': 'visible'});
                    $(this).unbind(e);
                }
            });

            cmd = false;
        } else {
            opts = {
                out: 'Username:',
                ps: '> ',
                next: 'useradd %cmd%',
            };
            cmd = false;
        }

        $ptty.set_command_option(opts);

        return cmd;
    }
};

const cmd_useradd = {
    name: 'useradd',
    method: function (cmd) {
        const xhr = $.ajax({
            url: "/index.php/api/users",
            method: "POST",
            data: {"username": cmd[1], "password": cmd[2], "isAdmin": cmd[3]},
            type: "json"
        });
        xhr.done(function () {
            $ptty.echo('User created');
        });
        xhr.fail(function (jqXHR, textStatus, message) {
            $ptty.echo('Error creating user ' + message);
        });
        return cmd;
    },
    options: [1, 2, 3],
    help: 'Create a new user. Usage: useradd [username] [password] [is_admin]'
};

const cmd_userdel = {
    name: 'userdel',
    method: function (cmd) {

        if (typeof cmd[1] === "undefined") {
            cmd.out = 'User name is required';
            return cmd;
        }

        const xhr = $.ajax({
            url: "/index.php/api/users/" + cmd[1],
            method: "DELETE",
            type: "json"
        });
        xhr.done(function () {
            $ptty.echo('User deleted');
        });
        xhr.fail(function (jqXHR, textStatus, message) {
            $ptty.echo('Error deleting user ' + message);
        });
        return cmd;
    },
    options: [1],
    help: 'Delete a user. Usage: userdel username'
};


//endregion

//region groups

let cmd_groups = {
    name: 'groups',
    method: function (cmd) {
        let groups = [];
        $.get({url: '/index.php/api/groups', async: false}).done(function (res) {
            groups = res;
        });
        cmd.out = groups.map(function (v) {
            return v.name;
        }).join(' ');
        return cmd;
    },
    options: [],
    help: 'List existing groups.'
};

let cmd_group = {
    name: 'group',
    help: 'Print group and users information about the specified GROUP',
    method: function (cmd) {
        if (typeof cmd[1] === "undefined") {
            cmd.out = 'Group name is required';
            return cmd;
        }
        $.get({url: '/index.php/api/groups/' + cmd[1], async: false}).done(function (res) {
            cmd.out = 'gid=' + res.id + '(' + res.name + ') is_admin=' + (res.isAdmin ? 'Y' : 'N') + ' users=';
            if (res.users.lenght) {
                for (let usr of res.users) {
                    cmd.out += '' + usr.id + '(' + usr.name + '),';
                }
                cmd.out = cmd.out.substring(0, cmd.out.length - 1);
            }
        }).fail(function (xhr) {
            if (xhr.status === 404) {
                cmd.out = 'Group not found';
            }
        });
        return cmd;
    },
    options: [1]
};
//endregion

let cmd_logout = {
    name: 'logout',
    help: 'Logout user session',
    method: function (cmd) {
        $.ajaxSetup({headers: []});
        cmd.rsp_batch_unregister = ['id', 'users', 'logout', 'groups', 'group', 'useradd'];
        $ptty.register('callbefore', cbf_login);
        $ptty.register('command', cmd_login);
        $ptty.register('callback', cbk_login);

        cmd.out = 'Logged out';
        cmd.ps = '$';
        $ptty.change_settings({ps: cmd.ps});
        setTimeout(() => $ptty.run_command('login'), 1);
        return cmd;
    }
};


const cbk_login = {
    name: 'login',
    method: function (cmd) {
        if (cmd.data && cmd.data.is_loggedin && cmd.data.is_loggedin === true) {
            // remove these commands using a response
            cmd.rsp_batch_unregister = ['login'];

            $ptty.register('command', cmd_users);
            $ptty.register('command', cmd_id);
            $ptty.register('command', cmd_groups);
            $ptty.register('command', cmd_group);
            //
            // $ptty.register('command', cmd_passwd);

            if (cmd.data.isAdmin) {
                $ptty.register('callbefore', cbf_useradd);
                $ptty.register('command', cmd_useradd);
                $ptty.register('command', cmd_userdel);
                // $ptty.register('command', cmd_usermod);
                // $ptty.register('command', cmd_groupadd);
                // $ptty.register('command', cmd_groupdel);
            }


            $ptty.register('command', cmd_logout);
        }
        return cmd;
    }
};
$ptty.register('callback', cbk_login);


const rsp_batch_unregister = {
    name: 'rsp_batch_unregister',
    method: function (cmd) {
        // commands to remove
        const cmd_names = cmd.rsp_batch_unregister;

        // from these stacks
        const stacks = ['callbefore', 'command', 'callback'];
        for (let i = 0; i < stacks.length; i++) {
            for (let n = 0; n < cmd_names.length; n++) {
                $ptty.unregister(stacks[i], cmd_names[n]);
            }
        }

        // Always delete your response property if you
        // don't want it to fire in unexpected places.
        delete (cmd.rsp_batch_unregister);

        return cmd;
    }
};
$ptty.register('response', rsp_batch_unregister);


$ptty.run_command('login');
