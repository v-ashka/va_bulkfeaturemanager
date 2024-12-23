export async function fetchData(url) {
  try {
    const response = await fetch(url);

    if (!response.ok) {
      throw new Error(`HTTP error! status: ${response.status}`);
    }

    return await response.json();

  } catch (error) {
    console.error('Fetch error:', error);
    throw error;
  }
}

export async function postData(url, bodyValue) {
  try {
    const response = await fetch(url, {
      method: "POST",
      body: JSON.stringify({bodyValue}),
      headers: {
        "Content-Type": "application/json; charset=UTF-8"
      }
    });

    if (!response.ok) {
      throw new Error(`HTTP error! status: ${response.status}`);
    }

    return await response.json();
  } catch (error) {
    console.error('Fetch error:', error);
    throw error;
  }
}
