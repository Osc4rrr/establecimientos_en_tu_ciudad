<template>
    <div>
        <vue-page-transition name="fade-in-right">
            <router-view></router-view>
        </vue-page-transition>
        
    </div>
</template>


<script>

import store from '../store/index';

export default {

    store
    
}
</script>