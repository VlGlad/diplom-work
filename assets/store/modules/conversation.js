import Vue from 'vue';

export default {
    state: {
        conversations: [],
        hubUrl: null
    },
    getters: {
        CONVERSATIONS: state => {
            return state.conversations.sort((a, b) => {
                return a.createdAt < b.createdAt;
            })
        },
        MESSAGES: state => conversationId => {
            return state.conversations.find(i => i.conversationId === conversationId).messages
        },
        HUBURL: state => state.hubUrl
    },
    mutations: {
        SET_CONVERSATIONS: (state, payload) => {
            state.conversations = payload
        },
        SET_MESSAGES: (state, {conversationId, payload}) => {
            Vue.set(
                state.conversations.find(i => i.conversationId === conversationId),
                'messages',
                payload
            )
        },
        ADD_MESSAGE: (state, {conversationId, payload}) => {
            let conv = state.conversations.find(i => i.conversationId === conversationId);
            //console.log(conv.messages[conv.messages.length - 1]);
            // conv.messages.push(payload);
            if (conv.messages[conv.messages.length - 1].id == payload.id){
                conv.messages[conv.messages.length - 1].mine=true;
                return;
            }
            conv.messages.push(payload);
        },
        SET_CONVERSATION_LAST_MESSAGE: (state, {conversationId, payload}) => {
            let rs = state.conversations.find(i => i.conversationId === conversationId);
            rs.content = payload.content;
            rs.createdAt = payload.createdAt;
        },
        SET_HUBURL: (state, payload) => state.hubUrl = payload,
        UPDATE_CONVERSATIONS: (state, payload) => {
            let rs = state.conversations.find(i => i.conversationId === payload.conversation.id);
            rs.content = payload.content;
            rs.createdAt = payload.createdAt;
        }
    },
    actions: {
        GET_CONVERSATIONS: ({commit}) => {
            return fetch(`/app/conversations`)
                .then(result => {
                    const hubUrl = result.headers.get('Link').match(/<([^>]+)>;\s+rel=(?:mercure|"[^"]*mercure[^"]*")/)[1]
                    commit("SET_HUBURL", hubUrl)
                    return result.json()
                })
                .then((result) => {
                    commit("SET_CONVERSATIONS", result)
                })
        },
        GET_MESSAGES: ({commit, getters}, conversationId) => {
            console.log("From ConvJS:"+conversationId);
            if (getters.MESSAGES(conversationId) === undefined) {
                return fetch(`/app/messages/${conversationId}`)
                    .then(result => result.json())
                    .then((result) => {
                        commit("SET_MESSAGES", {conversationId, payload: result})
                    });
            }

        },
        POST_MESSAGE: ({commit}, {conversationId, content}) => {
            let formData = new FormData();
            formData.append('content', content);

            return fetch(`/app/messages/${conversationId}`, {
                method: "POST",
                body: formData
            })
                .then(result => result.json())
                .then((result) => {
                    console.log(result);
                    commit("ADD_MESSAGE", {conversationId, payload: result})
                    commit("SET_CONVERSATION_LAST_MESSAGE", {conversationId, payload: result})
            })
        }
    }
}