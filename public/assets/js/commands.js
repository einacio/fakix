/*globals $ptty*/
window.$ptty.register('command', {
    name: 'users',
    method: function(cmd){
        let users= [];
        $.get({url:'/index.php/api/users', async:false}).done(function(res){users=res;});
        cmd.out = users.map(function(v){return v.name;}).join(' ');
        return cmd;
    },
    options: [],
    help: 'List users.'
});


