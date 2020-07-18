import Campaign from './components/campaign/Campaign.vue';

export const routes = [

    { 
        path: '*', components: 
        {
            default: Campaign
        }, 
    },
    
];