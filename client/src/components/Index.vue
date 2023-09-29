<template>
    <div id="container">
        <el-dialog title="预览" :visible.sync="preview" width="80%">
            <Preview :previewUrl="previewUrl" :type="type" v-if="preview"></Preview>
        </el-dialog>

        <div id="menu">
            <div class="menu-btn">
                <div icon="el-icon-folder" circle />
            </div>
            <div class="menu-btn">
                <el-button icon="el-icon-menu" circle />
            </div>
            <div class="menu-btn">
                <el-button icon="el-icon-chat-line-square" circle />
            </div>
            <div class="menu-btn">
                <el-button icon="el-icon-setting" circle />
            </div>
            <div class="menu-btn">
                <el-button icon="el-icon-user" circle />
            </div>
        </div>
        
        <div id="main">
            <div id="operation-box">
                <div>
                    <el-button icon="el-icon-back" circle type="primary" @click="handleToParent" />
                    <el-button type="primary" @click="handleUpload">上传文件</el-button>
                    <input ref="fileRef" type="file" @change="handleChange($event)" />
                </div>
                <div>
                    <span>欢迎你，用户{{ username }}</span>
                    <el-button type="primary" @click="handleExit">注销</el-button>
                </div>
            </div>
            <div id="file-list-box">
                <el-table :data="filelist" :default-sort = "{prop: 'icon'}" stripe height="480px">
                    <el-table-column prop="icon" label="*" width="50">
                        <template slot-scope="scope">
                            <span v-if="scope.row.icon == '0folder'" class="el-icon-folder" />
                            <span v-if="scope.row.icon == '1image'" class="el-icon-picture-outline" />
                            <span v-if="scope.row.icon == '2video'" class="el-icon-film" />
                            <span v-if="scope.row.icon == '3audio'" class="el-icon-microphone" />
                            <span v-if="scope.row.icon == '4doc'" class="el-icon-suitcase" />
                            <span v-if="scope.row.icon == '5text'" class="el-icon-edit" />
                            <span v-if="scope.row.icon == '6file'" class="el-icon-document" />
                        </template>
                    </el-table-column>
                    <el-table-column prop="basename" label="文件名称" />
                    <el-table-column prop="size" label="文件大小" />
                    <el-table-column prop="lastmod" label="修改时间" />
                    <el-table-column label="操作">
                        <template slot-scope="scope">
                            <el-button size="mini" type="primary"
                            @click="handleOpen(scope.$index, scope.row)">打开</el-button>
                            <el-button size="mini" type="success"
                            @click="handleDownload(scope.$index, scope.row)">下载</el-button>
                            <el-button size="mini" type="danger"
                            @click="handleDelete(scope.$index, scope.row)">删除</el-button>
                        </template>
                    </el-table-column>
                </el-table>
            </div>
            <p>Designed by Koorye@2023, all copyright reserved</p>
        </div>
    </div>
</template>

<script>
import Vue from 'vue'
import VuePdf from "vue-pdf"
import Preview from '@/components/Preview.vue'

export default {
    data() {
        return {
            username: '',
            client: '',
            currentDir: '/',
            filelist: [],
            dstPath: '',

            // for preview
            data: '',
            previewUrl: '',
            numPages: 0,
            preview: false
        }
    },
    components: {
        Preview
    },
    mounted: function() {
        this.username = Vue.prototype.$username
        this.client = Vue.prototype.$client
        this.getDirectory()
    },
    methods: {
        async getDirectory() {
            let result = await this.client.getDirectoryContents(this.currentDir, {
                details: true
            })
            let filelist = result.data

            console.log('get directory:', filelist)

            for (let idx in filelist) {
                // set size
                let size = filelist[idx].size
                size = size / 1024
                let unit = 'KB'
                if (size > 1024 * 1024) {
                    size = size / (1024 * 1024)
                    unit = 'GB'
                } else if (size > 1024) {
                    size = size / 1024
                    unit = 'MB'
                }
                filelist[idx].size = Math.ceil(size) + unit

                // set icon
                if (filelist[idx].type == 'directory') {
                    filelist[idx].icon = '0folder'
                } else {
                    filelist[idx].icon = this._getType(filelist[idx].basename)
                }

            }

            this.filelist = filelist
        },
        async handleOpen(index, row) {
            this.$notify.info({
                title: '消息',
                message: '文件打开中'
            })

            if (row.type == 'directory') {
                this.currentDir = row.filename
                this.getDirectory()
            } else {
                let type = this._getType(row.filename)

                if (['0text', '1image', '2video', '3audio', '4doc', '5text'].indexOf(type) == -1) {
                    this.$notify.error({
                        title: '消息',
                        message: '该文件不支持预览'
                    })
                    return
                }

                // preview text
                if (type == '5text') {
                    this.client.getFileContents(row.filename, {format: 'text'}).then(res => {
                        this.previewUrl = res
                        this.type = type
                        this.preview = true
                    }) 
                }

                // preview blobs
                if (['1image', '2video', '3audio', '4doc'].indexOf(type) > -1) {
                    this.client.getFileContents(row.filename).then(res => {
                        let binaryData = []
                        binaryData.push(res)
                        binaryData = new Blob(binaryData)
                        let previewUrl = window.URL.createObjectURL(binaryData)

                        this.previewUrl = previewUrl
                        this.type = type
                        this.preview = true
                    })
                }
            }
        },
        async handleDownload(index, row) {
            this.$notify.info({
                title: '消息',
                message: '文件下载中'
            })
            let link = await this.client.getFileDownloadLink(row.filename)
            window.open(link, '_self')
        },
        async handleDelete(index, row) {
            this.$notify.info({
                title: '消息',
                message: '文件删除中'
            })
            await this.client.deleteFile(row.filename).then(res => {
                this.getDirectory()
                this.$notify({
                    title: '消息',
                    message: '文件删除成功',
                    type: 'success'
                })
            })
        },
        handleToParent() {
            this.$notify.info({
                title: '消息',
                message: '返回中'
            })

            let currentDir = this.currentDir

            if (currentDir == '/') {
                this.$notify({
                    title: '消息',
                    message: '已经是根目录',
                    type: 'warning'
                })
                return
                return
            }

            currentDir = currentDir.split('/')
            currentDir = currentDir.slice(0, -1)
            currentDir = currentDir.join('/')

            if (currentDir == '') {
                currentDir = '/'
            }

            console.log(currentDir)
            this.currentDir = currentDir
            this.getDirectory()
        },
        async handleUpload() {
            if (this.dstPath == '' || this.data == '') {
                this.$notify.error({
                    title: '消息',
                    message: '未选择文件'
                })
                return
            }

            console.log('upload file', this.dstPath, this.data)
            this.$notify.info({
                title: '消息',
                message: '文件上传中'
            })

            await this.client.putFileContents(this.dstPath, this.data).then(res => {
                this.getDirectory()
                this.$notify({
                    title: '提示',
                    message: '文件上传成功',
                    type: 'success'
                })
            })
        },
        handleChange(e) {
            let file = e.target.files[0]
            let reader = new FileReader()
            let currentDir = this.currentDir
            reader.readAsArrayBuffer(file)
            
            var that = this
            reader.onload = function(e) {
                let dstPath = [currentDir, file.name].join('/')
                let data = e.target.result;

                that.dstPath = dstPath
                that.data = data

                this.$notify({
                    title: '消息',
                    message: '文件选择成功',
                    type: 'success'
                })
            }
        },
        handleExit() {
            this.$router.push('/login')
            this.$notify({
                title: '消息',
                message: '注销成功',
                type: 'warning'
            })
        },
        _getType(filename) {
            let suffix = filename.split('.')[1]
            if (['jpg', 'png', 'bmp', 'jpeg', 'gif'].indexOf(suffix) > -1) {
                return '1image'
            } else if (['mp4', 'avi', 'flv', 'mov'].indexOf(suffix) > -1) {
                return '2video'
            } else if (['mp3', 'wav', 'm4a'].indexOf(suffix) > -1) {
                return '3audio'
            } else if (['pdf', 'doc', 'docx'].indexOf(suffix) > -1) {
                return '4doc'
            } else if (['txt', 'md'].indexOf(suffix) > -1) {
                return '5text'
            } else {
                return '6file'
            }
        }
    }
}

</script>

<style scoped>
#container {
    width: 100%;
    height: 100vh;
    display: flex;
    justify-content: space-between;
    background-color: #FFFFFF;
    background-size: 100% 100%;
}

#menu {
    width: 5%;
    background-color: #2D3A4B;
}

#main {
    width: 95%;
    text-align: center;
}

#operation-box {
    display: flex;
    justify-content: space-between;
    background-color: white;
    padding: 1%;
}

.menu-btn {
    margin: 20px 15%;
}

p {
    font-size: 7pt;
}
</style>