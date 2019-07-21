/*globals $ptty*/

//based on the docs example
let cbf_login = {
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
    options: [1,2],
    help: 'Login command. Usage: login [username] [password]'
};
$ptty.register('command', cmd_login);


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
        if(typeof cmd[1] === "undefined"){
            cmd[1] = 0;
        }
        $.get({url: '/index.php/api/users/'+cmd[1], async: false}).done(function (res) {
            cmd.out = 'uid='+res.id+'('+res.name+') groups=';
            if(res.groups.lenght) {
                for (let grp of res.groups) {
                    cmd.out += '' + grp.id + '(' + grp.name + '),';
                }
                cmd.out = cmd.out.substring(0, cmd.out.length -1);
            }
        });
        return cmd;
    },
    options: [1]
};

let cmd_logout = {
   name: 'logout',
   help: 'Logout user session',
   method: function (cmd) {
       $.ajaxSetup({headers:{}});
       cmd.rsp_batch_unregister = ['id', 'users', 'logout'];
       $ptty.register('callbefore', cbf_login);
       $ptty.register('command', cmd_login);

       cmd.out = 'Logged out';
       cmd.ps = '$';
       $ptty.change_settings({ps: cmd.ps});
       setTimeout(()=>$ptty.run_command('login'),1);
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
            // $ptty.register('command', cmd_groups);
            // $ptty.register('command', cmd_group);
            //
            // $ptty.register('command', cmd_passwd);

            if(cmd.data.isAdmin){
                // $ptty.register('command', cmd_useradd);
                // $ptty.register('command', cmd_userdell);
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
