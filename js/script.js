fetch('https://rebornian48.github.io/data/json/member.json')
  .then(response => {
    if (!response.ok) {
      throw new Error('Network response was not ok');
    }
    return response.json();
  })
  .then(data => {
    console.log('Fetched Data:', data);
  })
  .catch(error => {
    console.error('Error:', error);
  });