import axios from 'axios';
import get from 'lodash/get';

axios.interceptors.response.use(response => response, async err => {
  const status = get(err, 'response.status')

  if (status === 419) {
    // Refresh our session token
    await axios.get('/refresh-csrf-token')

    // Return a new request using the original request's configuration
    return axios(err.response.config)
  }

  return Promise.reject(err)
})