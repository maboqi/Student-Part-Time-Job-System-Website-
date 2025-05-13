
// 引入 Firebase 模块
import { initializeApp } from "https://www.gstatic.com/firebasejs/11.0.2/firebase-app.js";
import { getDatabase, ref, set, push, onValue, get, onChildAdded } from "https://www.gstatic.com/firebasejs/11.0.2/firebase-database.js";

// Firebase 配置
const firebaseConfig = {
    apiKey: "AIzaSyA654TLJk9Uxt5l3SRLgmrXHC3zmxxWKy0",
    authDomain: "campush-e4191.firebaseapp.com",
    databaseURL: "https://campush-e4191-default-rtdb.firebaseio.com",
    projectId: "campush-e4191",
    storageBucket: "campush-e4191.firebasestorage.app",
    messagingSenderId: "215978193916",
    appId: "1:215978193916:web:cfa57d7e907bb8f5ceb879"
};

// 初始化 Firebase
const app = initializeApp(firebaseConfig);
const db = getDatabase(app);



const currentUser = {
    //userid: users.user_id,
    employerid: 1,
    employer: "Current User",
    //phone: "1111111111"
}


// 获取用户信息，如果已经存在聊天室，则之前的保留不变
fetch("fetch_useremployer.php")
.then(response => response.json())
.then(data => {
    console.log('User ID:', data.user_id); // 访问 user_id
    console.log('Usersname:', data.user_name);
    console.log('Users:', data.users); // 访问 users 数组
    currentUser.employerid = data.user_id;
    currentUser.employer = data.user_name;


        // 从 Firebase 获取已有的聊天室列表
    const chatroomsRef = ref(db, 'chatrooms/');
    get(chatroomsRef).then(snapshot => {
        const existingChatrooms = snapshot.val() || {};

        data.users.forEach(user => {

                    // 生成聊天室ID
            const chatroomId = `chat_E${currentUser.employerid}_S${user.studentid}`;
            const chatroomId2 = `chat_S${user.studentid}_E${currentUser.employerid}`;
            const chatroomRef = ref(db, `chatrooms/${chatroomId}`);

                    // 检查聊天室是否已经存在
            if (!(existingChatrooms[chatroomId] || existingChatrooms[chatroomId2])) {
                        // 如果聊天室不存在，则创建
                set(chatroomRef, {
                    user1: currentUser,
                    user2: user,
                    messages: []
                }).then(() => {
                    console.log(`Room of：E${currentUser.employer} and S${user.student}`);
                }).catch(error => console.error("Failed creating room:", error));
            } else {
                console.log(`Room already existed：E${currentUser.employer} and S${user.student}`);
            }
            
        });

            // 渲染联系人列表
        const contactList = document.getElementById("contact-list");
        data.users.forEach(user => {
            const contact = document.createElement("div");
            contact.innerText = user.student;
            contact.addEventListener("click", () => openChatroom(user));
            contactList.appendChild(contact);
            
        });
    });
});



// 全局变量保存当前聊天室 ID 和 Firebase 引用
let currentChatroomId = null;

// 打开聊天室
function openChatroom(user) {
    // 设置当前聊天室 ID,确保1_2和2_1是一个房间
    currentChatroomId = `chat_E${currentUser.employerid}_S${user.studentid}`;
    const chatroomRef = ref(db, `chatrooms/${currentChatroomId}`);

    // 加载消息
    loadMessages(chatroomRef);

    // 更新聊天窗口标题
    document.querySelector("#chat-room h2").innerText = `Chat with ${user.student} (Student)`;

    fetch("fetch_appe.php")
    .then(response => response.json())
    .then(data => {
        console.log('studentinfos:', data.studentinfo);

        data.studentinfo.forEach(student => {
            if(user.studentid == student.userid){
                document.getElementById("picture").src = student.pic_data;
                document.getElementById("picture").width = 300;  // 设置图片宽度为200px
                document.getElementById("picture").height = 300; 
                document.getElementById("faculty").textContent = `Faculty: ${student.faculty}`;
                document.getElementById("phone").textContent = student.phone;
                document.getElementById("email").textContent = student.email;
            }
            
        });

    });
    

}

// 加载消息
function loadMessages() {
    console.log("loadMessages function called");
    console.log(`Current chatroom ID: ${currentChatroomId}`);

    const messagesDiv = document.getElementById("messages");

    // 检查 messagesDiv 是否存在
    if (!messagesDiv) {
        console.error("Element with ID 'messages' not found!");
        return;
    }

    // 清空当前房间的消息区域，避免切入到另一个房间时，之前房间的消息还在显示
    messagesDiv.innerHTML = "";

    // 使用 onChildAdded 来实时监听消息变化
    const messagesRef = ref(db, `chatrooms/${currentChatroomId}/messages`);
    //还要清空一次，避免输入的内容重复输出，如input = ee, output = ee,ee
    messagesDiv.innerHTML = "";

    // 监听消息添加（不会重复加载所有消息）
    onChildAdded(messagesRef, (snapshot) => {
        const message = snapshot.val();
        const messageDiv = document.createElement("div");
        
        messageDiv.innerText = `${message.sender}: ${message.text}`;
        messagesDiv.appendChild(messageDiv);

        // 滚动到底部
        messagesDiv.scrollTop = messagesDiv.scrollHeight;
    });

    // 如果当前没有消息，可以显示一个默认提示
    onValue(messagesRef, (snapshot) => {
        const messages = snapshot.val();
        if (!messages) {
            const messageDiv = document.createElement("div");
            messageDiv.innerText = `No messages yet. Send one to start the conversation.`;
            messagesDiv.appendChild(messageDiv);
        }
    });
}

// 发送消息
window.sendMessage = function () {
    const messageInput = document.getElementById("message");
    const messageText = messageInput.value.trim();

    if (messageText && currentChatroomId) {
        const messagesRef = ref(db, `chatrooms/${currentChatroomId}/messages`);
        push(messagesRef, {
            sender: currentUser.employer,
            text: messageText,
            timestamp: Date.now()
        }).then(() => {
            messageInput.value = ""; // 清空输入框
        }).catch(error => console.error("Error sending message:", error));
    }
};


