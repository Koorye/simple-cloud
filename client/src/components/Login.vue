<template>
    <div id="container">
        <div id="login-box">
            <h2>简单云</h2>
            <el-input placeholder="请输入服务器域名，如: http://localhost/webdav" v-model="host"></el-input>
            <el-input placeholder="请输入用户名" v-model="username"></el-input>
            <el-input placeholder="请输入密码" v-model="password" show-password></el-input>
            <el-button type="primary" @click="handle_login">登录</el-button>
            <p>Designed by Koorye@2023, all copyright reserved</p>
        </div>
    </div>
</template>

<script>
import { createClient } from "webdav"
import Vue from 'vue'

export default {
  data() {
    return {
        host: 'http://localhost:15000/remote.php/dav/files/koorye',
        username: 'koorye',
        password: 'koorye'
    }
  }, 
  methods: {
    handle_login: async function() {
        const client = createClient(this.host, {
            username: this.username,
            password: this.password
        })

        client.getDirectoryContents('/',{details: true}).then(result => {
            console.log('login!')

            Vue.prototype.$username = this.username
            Vue.prototype.$client = client

            this.$router.push({name: 'Index'})
            this.$notify({
                title: '提示',
                message: '登录成功',
                type: 'success'
            })
        }).catch(result => {
            this.$notify.error({
                title: '登录失败',
                message: '用户名、密码或域名错误'
            })
        })

    }
  }
}
</script>

<style scoped>
#container {
    background-color: #2D3A4B;
    width: 100%;
    height: 100vh;
}

h2 {
    color: white;
}

#login-box {
    padding: 10% 30%;
    text-align: center;
}

.el-input, .el-button {
    margin: 1%;
}

button {
    width: 100%;
}
    
p {
    color: white;
}
</style>